<?php

namespace Shaferllc\Analytics\Http\Controllers\API;

use App\Models\Site;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Streamline\Classes\Agent;
use Illuminate\Support\Carbon;
use Streamline\Jobs\Screenshot;
use Illuminate\Routing\Pipeline;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use GeoIp2\Database\Reader as GeoIP;
use Shaferllc\Analytics\Models\Meta;
use Shaferllc\Analytics\Models\Event;
use Shaferllc\Analytics\Models\PageVisitor;
use Symfony\Component\HttpFoundation\IpUtils;

class EventController
{
    /**
     * The tracking mechanism.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $siteId = $request->input('siteId');
        if (!$siteId) {
            return response()->json(['message' => 'Site ID is required.'], 400);
        }

        $site = Site::findOrFail($siteId);


        // if ($this->isExcludedIp($site, $request->ip()) || $this->isBot($site, $request->header('User-Agent'))) {
        //     return response(403);
        // }

        $data = $this->getVisitorData($request);

        return app(Pipeline::class)
        ->send(['data' => $data, 'site' => $site])
        ->through([
            \Shaferllc\Analytics\Pipelines\CreatePage::class,
            \Shaferllc\Analytics\Pipelines\UpdatePageMeta::class,
            \Shaferllc\Analytics\Pipelines\CreateVisitor::class,
            \Shaferllc\Analytics\Pipelines\HandleSessionStart::class,
            \Shaferllc\Analytics\Pipelines\HandleSessionEnd::class,
            \Shaferllc\Analytics\Pipelines\ProcessPageData::class,
            \Shaferllc\Analytics\Pipelines\ProcessTrafficSourceData::class,
            \Shaferllc\Analytics\Pipelines\ProcessBrowserData::class,
            \Shaferllc\Analytics\Pipelines\ProcessPerformanceMetricsData::class,
            \Shaferllc\Analytics\Pipelines\ProcessUserInteractionData::class,
            \Shaferllc\Analytics\Pipelines\ProcessDeviceData::class,
            \Shaferllc\Analytics\Pipelines\ProcessViewportChanges::class,
            \Shaferllc\Analytics\Pipelines\ProcessOutboundLinkClicks::class,
        ])
        ->then(function ($result) {
            // ray($result);
            return response(200);
        });

    }

    // private function isExcludedIp($site, $ip)
    // {
    //     if (!$site->exclude_ips) {
    //         return false;
    //     }

    //     $excludedIps = preg_split('/\n|\r/', $site->exclude_ips, -1, PREG_SPLIT_NO_EMPTY);
    //     return IpUtils::checkIp($ip, $excludedIps);
    // }

    // private function isBot($site, $userAgent)
    // {
    //     return $site->exclude_bots && strpos(strtolower($userAgent), 'bot') !== false;
    // }

    private function getPageUrl($page)
    {
        $url = $page['path'] ?? '/';
        if (isset($page['query']) && !empty($page['query'])) {
            parse_str($page['query'], $queryParams);

        }
        return mb_substr($url, 0, 255);
    }


    private function getVisitorData(Request $request): array
    {
        return array_merge(
            array_filter($request->all()),
            $request->has('ip') ? $this->getGeoData($request->input('ip')) : [],
            []
        );
    }

    private function getGeoData($ip)
    {
        try {
            $geoip = (new GeoIP(storage_path('app/geoip/GeoLite2-City.mmdb')))->city($ip);
            return [
                'continent' => $geoip->continent->code . ':' . $geoip->continent->name,
                'country' => $geoip->country->isoCode . ':' . $geoip->country->name,
                'city' => $geoip->country->isoCode . ':' . $geoip->city->name . (isset($geoip->mostSpecificSubdivision->isoCode) ? ', ' . $geoip->mostSpecificSubdivision->isoCode : ''),
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    // private function saveData($data, $site)
    // {

    //     $page = Arr::get($data, 'page');
    //     $path = Arr::get($data, 'path');
    //     $session_id = Arr::get($data, 'session_id');
    //     $title = Arr::get($data, 'title');
    //     $charset = Arr::get($data, 'charset');

    //     $events = Arr::get($data, 'events');

    //     // Start session
    //     $startSession = collect($events)->where('name', 'start_session')->first();

    //     // End session
    //     $endSession = collect($events)->where('name', 'end_session')->first();

    //     // Create page
    //     $page = $site->pages()->firstOrCreate([
    //         'path' => $path,
    //         'page' => $page,
    //     ]);

    //     // Update page meta
    //     $titles = collect($page->meta_data->get('title', []))->push($title)->unique()->values();
    //     $page->meta_data->set('title', $titles);

    //     // Update charset
    //     $charset = collect($page->meta_data->get('charset', []))->push($charset)->unique()->values();
    //     $page->meta_data->set('charset', $charset);
    //     $page->save();

    //     // Update visitor data
    //     $visitor = $page->visitors()->firstOrCreate(
    //         ['session_id' => $session_id],
    //         [
    //             'timezone' => Arr::get($startSession, 'value.time_zone'),
    //             'language' => Arr::get($startSession, 'value.language'),
    //             'user_id' => Arr::get($startSession, 'value.user_id'),

    //         ]
    //     );

    //     // Start session
    //     if ($startSession) {
    //         $page->visitors()->updateExistingPivot($visitor->id, [
    //             'last_visit_at' => now(),
    //             'total_visits' => ($page->visitors()->where('session_id', $session_id)->first()?->page_visitor?->total_visits ?? 0) + 1,
    //             'first_visit_at' => $page->visitors()->where('session_id', $session_id)->first()?->page_visitor?->first_visit_at ?? now(),
    //             'start_session_at' => isset($startSession) ? Carbon::parse(Arr::get($startSession, 'value.start_time')) : null,
    //         ]);
    //     }

    //     // End session
    //     if ($endSession) {
    //         $page->visitors()->updateExistingPivot($visitor->id, [
    //             'end_session_at' => isset($endSession) ? Carbon::parse(Arr::get($endSession, 'value.end_time')) : null,
    //             'total_duration_seconds' => ($page->visitors()->where('session_id', $session_id)->first()?->page_visitor?->total_duration_seconds ?? 0) + Arr::get($endSession, 'value.total_duration_seconds'),
    //             'total_time_spent' => ($page->visitors()->where('session_id', $session_id)->first()?->page_visitor?->total_time_spent ?? 0) + Arr::get($endSession, 'value.total_duration'),
    //         ]);

    //         // Create meta
    //         $meta = $visitor->page_visitor->meta()->firstOrCreate([
    //             'itemable_id' => $visitor->page_visitor->id,
    //             'itemable_type' => PageVisitor::class,
    //             'meta_type' => 'session_end',
    //         ]);

    //         // Update exit page
    //         $exit_url = Arr::get($endSession, 'value.exit_page');

    //         // Get existing exit pages
    //         $exit_pages = collect($meta->meta_data->get('exit_page', []));

    //         // Update or add exit page data
    //         if ($page = $exit_pages->firstWhere('url', $exit_url)) {
    //             // Update existing exit page
    //             $exit_pages->transform(fn($p) => $p['url'] === $exit_url ? ['url' => $p['url'], 'count' => $p['count'] + 1] : $p);
    //         } else {
    //             // Add new exit page
    //             $exit_pages->push(['url' => $exit_url, 'count' => 1]);
    //         }

    //         // Set meta
    //         $meta->meta_data->set('exit_page', $exit_pages->values())
    //             ->set('exit_page_count', [$exit_pages->count()]);
    //         $meta->save();

    //     }

    //     $visitor->save();

    //     $otherEvents = collect($events)->whereNotIn('name', ['start_session', 'end_session']);

    //     $viewportChange = $otherEvents->where('name', 'viewport_change')->all();

    //     foreach ($viewportChange as $event) {

    //         $parent_event = $visitor->events()->create([
    //             'site_id' => $site->id,
    //             'visitor_id' => $visitor->id,
    //             'page_id' => $page->id,
    //             'name' => Arr::get($event, 'name'),
    //             'timestamp' => Arr::get($event, 'timestamp', now()),
    //         ]);

    //         foreach ($event['value'] as $key => $value) {
    //             if (!is_array($value)) {
    //                 // Create parent meta record for non-array values
    //                 $parent = $parent_event->meta()->create([
    //                     'itemable_id' => $parent_event->id,
    //                     'itemable_type' => Event::class,
    //                     'meta_type' => $key,
    //                     'meta_data' => $value
    //                 ]);
    //             } else {
    //                 // Create parent meta record for array values
    //                 $parent = $parent_event->meta()->create([
    //                     'itemable_id' => $parent_event->id,
    //                     'itemable_type' => Event::class,
    //                     'meta_type' => $key,
    //                     'meta_data' => $value
    //                 ]);
    //                 // Create child meta records for each array item
    //                 foreach ($value as $k => $v) {
    //                     $parent->parentMeta()->create([
    //                         'itemable_id' => $parent_event->id,
    //                         'itemable_type' => Event::class,
    //                         'metaable_type' => Meta::class,
    //                         'metaable_id' => $parent->id,
    //                         'meta_type' => $key . '_' . $k,
    //                         'meta_data' => [$k => $v],
    //                         'parent_id' => $parent->id,
    //                         'parent_type' => $parent->getMorphClass(),
    //                     ]);
    //                 }
    //             }
    //         }
    //     }

    //     // $visitor->events()->createMany($otherEvents->toArray());
    //     // foreach ($otherEvents as $event) {
    //     //     $event->save();
    //     // }
    //     return;



    //     // Screenshot::dispatch($page->url);

    //     foreach ($events as $event) {



    //         $hasValueOnly = ['viewport_change'];
    //         if (in_array($event['name'], $hasValueOnly)) {
    //             $hashid = md5(json_encode($event['value']));
    //         } else {
    //             $hashid = md5($event['session_id'] . $event['user_id'] . $event['name'] . json_encode($event['value']));
    //         }

    //         if (Arr::has($event, 'timestamp')) {
    //             if (Arr::get($event, 'timezone')) {
    //                 $timestamp = Carbon::createFromTimestamp(Arr::get($event, 'timestamp'))->timezone(Arr::get($event, 'timezone'));
    //             } else {
    //                 $timestamp = Carbon::createFromTimestamp(Arr::get($event, 'timestamp'));
    //             }
    //         } else {
    //             $timestamp = now();
    //         }

    //         $commonData = [
    //             'category' => $event['name'],
    //             'site_id' => $site->id,
    //             'user_id' => $event['user_id'],
    //             'session_id' => $event['session_id'],
    //             'date' => $timestamp,
    //             'value' => $event['value']
    //         ];

    //         $stat = $page->stats()->updateOrCreate(
    //             ['hashid' => $hashid],
    //             $commonData
    //         );

    //         // if ($stat->wasRecentlyCreated) {
    //         //     ray(
    //         //         'Created new stat record',
    //         //         $stat->toArray()
    //         //     );
    //         // } else {
    //         //     ray(
    //         //         'Updated existing stat record',
    //         //         $stat->toArray()
    //         //     );
    //         // }

    //         $stat->increment('count');
    //     }
    //     // $this->saveRecentTraffic($request, $data, $site);
    // }

    // private function saveRecentTraffic(Request $request, $data, $site)
    // {
    //     $site->recents()->updateOrCreate([
    //         'session_id' => $data['session_id'],
    //         'page' => $data['page'],
    //     ], [
    //         'referrer' => $data['referrer'] ?? null,
    //         'os' => $data['os'] ?? null,
    //         'ip' => $data['ip'] ?? null,
    //         'timezone' => $data['timezone'] ?? null,
    //         'page_title' => $data['page_title'] ?? null,
    //         'user_agent' => $data['user_agent'] ?? null,
    //         'browser' => $data['browser'] ?? null,
    //         'device' => $data['device'] ?? null,
    //         'country' => $data['country'] ?? null,
    //         'city' => $data['city'] ?? null,
    //         'language' => $data['language'] ?? null,
    //         'created_at' => Carbon::now(),
    //     ]);
    // }

    /**
     * Returns the parsed URL, including an always "non-www." version of the host.
     *
     * @param string $url
     * @return array|null
     */
    // private function parseUrl($url)
    // {
    //     $parsedUrl = parse_url($url);

    //     if (!isset($parsedUrl['host'])) {
    //         return null;
    //     }

    //     $parsedUrl['non_www_host'] = ltrim($parsedUrl['host'], 'www.');

    //     return $parsedUrl;
    // }
}
