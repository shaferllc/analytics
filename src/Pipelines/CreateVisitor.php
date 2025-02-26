<?php

namespace Shaferllc\Analytics\Pipelines;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CreateVisitor
{
    public function handle($payload, \Closure $next)
    {
        $data = Arr::get($payload, 'data');
        $page = Arr::get($payload, 'page');
        $site = Arr::get($payload, 'site');
        $events = Arr::get($data, 'events', []);
        $sessionId = Arr::get($data, 'session_id') ?? 'manual_' . md5(uniqid('session', true));

        // Get page data once outside transaction
        $sessionData = collect($events)->where('name', 'user_session_data')->first();

        $result = DB::transaction(function() use ($page, $sessionId, $sessionData, $site) {

            // $geoData = $this->getGeoData(Arr::get($pageData, 'value.ip_address'));
            // Lock the visitor row exclusively to prevent deadlocks
            $visitor = $site->visitors()
                ->lockForUpdate()
                ->firstOrCreate(
                    ['session_id' => $sessionId],
                    [
                        'first_visit_at' => now(),
                        'last_visit_at' => now(),
                        'total_visits' => 1,
                        'city' => Arr::get($sessionData, 'value.city'),
                        'continent' => Arr::get($sessionData, 'value.continent'),
                        'country' => Arr::get($sessionData, 'value.country'),
                        'region' => Arr::get($sessionData, 'value.region'),
                        'ip_address' => Arr::get($sessionData, 'value.ip_address'),
                        'language' => Arr::get($sessionData, 'value.language'),
                        'time_zone' => Arr::get($sessionData, 'value.time_zone'),
                        'user_id' => Arr::get($sessionData, 'value.user_id'),

                    ]
                );

            if (!$visitor->wasRecentlyCreated) {
                $visitor->increment('total_visits');
                $visitor->update(['last_visit_at' => now()]);
            }

            // Create unique key for this specific page visit variation
            $visitKey = md5(implode('|', [
                Arr::get($sessionData, 'value.query', ''),
                Arr::get($sessionData, 'value.hash', ''),
                Arr::get($sessionData, 'value.url_query', '')
            ]));

            // Check if visitor has this specific page variation
            $existingPageVisit = $visitor->pages()
                ->wherePivot('page_id', $page->id)
                ->wherePivot('visit_key', $visitKey)
                ->first();

            // Also get the base page visit record (without considering variations)
            $basePageVisit = $visitor->pages()
                ->wherePivot('page_id', $page->id)
                ->wherePivot('is_base_record', true)
                ->first();

            // Common pivot data
            $pivotData = [
                'last_visit_at' => now(),
                'referrer' => Arr::get($sessionData, 'value.referrer'),
                'hash' => Arr::get($sessionData, 'value.hash'),
                'query' => Arr::get($sessionData, 'value.query'),
                'campaign' => Arr::get($sessionData, 'value.campaign'),
                'search_engine' => Arr::get($sessionData, 'value.search_engine'),
                'social_network' => Arr::get($sessionData, 'value.social_network'),
                'session_duration' => Arr::get($sessionData, 'value.session_duration'),
                'page_depth' => Arr::get($sessionData, 'value.page_depth'),
                'url_query' => Arr::get($sessionData, 'value.url_query'),
                'load_time' => Arr::get($sessionData, 'value.load_time'),
                'visit_key' => $visitKey
            ];

            // Handle base page record
            if (!$basePageVisit) {
                // Create base record if it doesn't exist
                $visitor->pages()->attach($page->id, array_merge($pivotData, [
                    'first_visit_at' => now(),
                    'total_visits' => 1,
                    'is_base_record' => true
                ]));
            } else {
                // Update base record
                $visitor->pages()->updateExistingPivot($page->id, [
                    'last_visit_at' => now(),
                    'total_visits' => $basePageVisit->page_visitor->total_visits + 1
                ], false);
            }

            // Handle specific variation
            if ($existingPageVisit) {
                // Update existing variation
                $visitor->pages()->updateExistingPivot($page->id, array_merge($pivotData, [
                    'total_visits' => $existingPageVisit->page_visitor->total_visits + 1
                ]), false);
            } else {

                // Create new variation record
                $visitor->pages()->attach($page->id, array_merge($pivotData, [
                    'first_visit_at' => now(),
                    'total_visits' => 1,
                    'is_base_record' => false,
                    'landing_page' => Arr::get($sessionData, 'value.landing_page'),
                    'start_time' => Arr::get($sessionData, 'value.start_time'),
                    'performance_metrics' => Arr::get($sessionData, 'value.performance_metrics'),
                    'navigation_type' => Arr::get($sessionData, 'value.navigation_type'),
                ]));
            }

            // Return the specific variation record
            $visitorPage = $visitor->pages()
                ->wherePivot('page_id', $page->id)
                ->wherePivot('visit_key', $visitKey)
                ->first()
                ->page_visitor;

            return ['visitor' => $visitor, 'visitorPage' => $visitorPage];
        }, 3);

        return $next(array_merge($payload, $result));
    }

    private function getGeoData($ip)
    {
        return [];
        try {
            $cacheKey = "geoip_data:{$ip}";
            return Cache::remember($cacheKey,0, function () use ($ip) {
                $geoip = geoip()->getLocation($ip);
                return [
                    "ip" => $ip,
                    "iso_code" => $geoip->isoCode,
                    "country" => $geoip->country,
                    "city" => $geoip->city,
                    "state" => $geoip->isoCode,
                    "state_name" => $geoip->name,
                    "postal_code" => $geoip->postalCode,
                    "lat" => $geoip->lat,
                    "lon" => $geoip->lon,
                    "timezone" => $geoip->timezone,
                    "continent" => $geoip->continent,
                    "currency" => $geoip->currency,
                ];
            });
        } catch (\Exception $e) {
            return [];
        }
    }
}
