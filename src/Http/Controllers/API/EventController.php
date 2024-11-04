<?php

namespace Shaferllc\Analytics\Http\Controllers\API;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Streamline\Classes\Agent;
use Illuminate\Support\Carbon;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use GeoIp2\Database\Reader as GeoIP;
use Shaferllc\Analytics\Models\Website;
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
        $websiteId = $request->input('websiteId');
        if (!$websiteId) {
            return response()->json(['message' => 'Website ID is required.'], 400);
        }

        $website = Website::findOrFail($websiteId);

        if (!$website->can_track) {
            return response(403);
        }

        if ($this->isExcludedIp($website, $request->ip()) || $this->isBot($website, $request->header('User-Agent'))) {
            return response(403);
        }

        $data = $this->prepareData($request, $website);

        $this->saveData($data, $website);

        return response(200);
    }

    private function isExcludedIp($website, $ip)
    {
        if (!$website->exclude_ips) {
            return false;
        }

        $excludedIps = preg_split('/\n|\r/', $website->exclude_ips, -1, PREG_SPLIT_NO_EMPTY);
        return IpUtils::checkIp($ip, $excludedIps);
    }

    private function isBot($website, $userAgent)
    {
        return $website->exclude_bots && strpos(strtolower($userAgent), 'bot') !== false;
    }

    private function prepareData(Request $request, $website)
    {

        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H');
      
        $events = [];

        if ($request->input('events')) {
            foreach ($request->input('events') as $event) {
                $events[] = $this->prepareEventData($event);
            }
        }

        return $this->preparePageViewData($request, $website, $date, $time, $events);
    }

    private function prepareEventData($event)
    {
        if (empty($event['name'])) {
            return [];
        }

        $eventName = str_replace(':', ' ', $event['name']);
        $eventValue = null;

        // Normal event handling
        if (!empty($event['value'])) {
            if (is_array($event['value'])) {
                $eventValue = implode(',', array_map(function($key, $value) {
                    return is_array($value) ? "$key:" . json_encode($value) : "$key:$value";
                }, array_keys($event['value']), $event['value']));
            } elseif (is_numeric($event['value']) && $event['value'] > 0 && strlen($event['value']) <= 10) {
                $eventValue = trim($event['value']);
            }
        }

        $eventUnit = !empty($event['unit']) && mb_strlen($event['unit']) <= 32 ? $event['unit'] : null;

        return [$eventName => implode(':', array_filter([$eventValue, $eventUnit]))];
    }

    private function preparePageViewData(Request $request, $website, $date, $time, $events)
    {
        $data = [
            'pageviews_date' => $date,
            'pageviews_hour' => $time,
            // 'query_params' => $page['query'],
        ];

        foreach ($events as $event) {
            $data = array_merge($data, $event);
        }
        $referrer = $this->parseUrl($request->input('referrer'));

        if ($this->isUniqueVisit($referrer, $website)) {
            $data = array_merge($data, $this->getVisitorData($request, $referrer, $website));
        }

        return $data;
    }

    private function getPageUrl($page)
    {
        $url = $page['path'] ?? '/';
        if (isset($page['query']) && !empty($page['query'])) {
            parse_str($page['query'], $queryParams);
         
        }
        return mb_substr($url, 0, 255);
    }

    private function isUniqueVisit($referrer, $website)
    {
        return !isset($referrer['non_www_host']) || $referrer['non_www_host'] != $website->domain;
    }

    private function getVisitorData(Request $request, $referrer, $website)
    {
        $geoData = $this->getGeoData($request->input('ip'));

        $basicData = [
            'browser' => $request->input('browser'),
            'browser_version' => $request->input('browser_version'),
            'city' => $geoData['city'] ?? $request->input('city'),
            'country' => $geoData['country'] ?? $request->input('country'),
            'device' => $request->input('device'),
            'domain' => $request->input('domain'),
            'ip' => $request->input('ip'),
            'language' => $request->input('language'),
            'page' => $this->getPageUrl($this->parseUrl($request->input('page'))),
            'page_title' => $request->input('page_title'),
            'referrer' => $request->input('referrer'),
            'os' => $request->input('os'),
            'session_id' => $request->input('session_id'),
            'timezone' => $request->input('timezone'),
            'url_path' => $request->input('url_path'),
            'url_query' => $request->input('url_query'),
            'user_agent' => $request->input('user_agent'),
        ];

        $geoData = [
            // 'city' => $geoData['city'] ?? null,
            // 'continent' => $geoData['continent'] ?? null,
            // 'country' => $geoData['country'] ?? null,
        ];

        $dates = [
            'visitors' => Carbon::now()->format('Y-m-d'),
            'visitors_hour' => Carbon::now()->format('H'),
        ];

        $allData = array_filter(array_merge($basicData, $geoData, $dates, [
            // 'campaign' => $request->input('utm_campaign'),
            // 'connection_downlink' => $request->input('connection_downlink'),
            // 'connection_rtt' => $request->input('connection_rtt'),
            // 'connection_save_data' => $request->input('connection_save_data'),
            // 'connection_type' => $request->input('connection_type'),
            // 'cookies_enabled' => $request->input('cookies_enabled'),
            // 'device' => $request->input('device_type'),
            // 'device_memory' => $request->input('device_memory'),
            // 'hardware_concurrency' => $request->input('hardware_concurrency'),
            // 'ip_address' => $request->input('ip_address'),
            // 'is_new_session' => $request->input('session_id') !== $request->input('pa_session_id'),
            // 'java_enabled' => $request->input('java_enabled'),
            // 'landing_page' => $this->getPageUrl($this->parseUrl($request->input('page'))),
            // 'language' => $request->input('language'),
            // 'language_preference' => $request->input('language_preference'),
            // 'max_touch_points' => $request->input('max_touch_points'),
            // 'network_type' => $request->input('network_type'),
            // 'on_line' => $request->input('on_line'),
            // 'plugins' => $request->input('plugins'),
            // 'resolution' => $request->input('screen_resolution'),
            // 'screen_avail_height' => $request->input('screen_avail_height'),
            // 'screen_avail_width' => $request->input('screen_avail_width'),
            // 'screen_color_depth' => $request->input('screen_color_depth'),
            // 'screen_height' => $request->input('screen_height'),
            // 'screen_orientation' => $request->input('screen_orientation'),
            // 'screen_orientation_angle' => $request->input('screen_orientation_angle'),
            // 'screen_orientation_type' => $request->input('screen_orientation_type'),
            // 'screen_pixel_depth' => $request->input('screen_pixel_depth'),
            // 'screen_resolution' => $request->input('screen_resolution'),
            // 'screen_width' => $request->input('screen_width'),
            // 'service_worker_status' => $request->input('service_worker_status'),
            // 'touch_support' => $request->input('touch_support'),
            // 'vendor' => $request->input('vendor'),
            // 'vendor_sub' => $request->input('vendor_sub'),   
            // 'viewport_height' => $request->input('viewport_height'),
            // 'viewport_width' => $request->input('viewport_width'),
            // 'webgl_support' => $request->input('webgl_support'),
            // 'worker_support' => $request->input('worker_support')
        ]));

        return $allData;
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

    private function saveData($data, $website)
    {             
        foreach ($data as $name => $value) {
            $website->stats()->updateOrCreate(
                [
                    'name' => $name,
                    'date' => Carbon::now()->format('Y-m-d'),
                    'page' => $data['page'],
                    'value' => mb_substr($value, 0, 255),
                    'session_id' => $data['session_id'],
                ],
                [
                    'count' => DB::raw('`count` + 1')
                ]
            );
        }


        if (!isset($data['event'])) {
            $this->saveRecentTraffic($data, $website);
        }
    }

    private function saveRecentTraffic($data, $website)
    {
        $website->recents()->updateOrCreate([
            'session_id' => $data['session_id'],
            'page' => $data['page'],
        ], [
            'referrer' => $data['referrer'] ?? null,
            'os' => $data['os'] ?? null,
            'ip' => $data['ip'] ?? null,
            'timezone' => $data['timezone'] ?? null,
            'page_title' => $data['page_title'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
            'browser_version' => $data['browser_version'] ?? null,
            'browser' => $data['browser'] ?? null,
            'device' => $data['device'] ?? null,
            'country' => $data['country'] ?? null,
            'city' => $data['city'] ?? null,
            'language' => $data['language'] ?? null,
            'created_at' => Carbon::now(),
        ]);
    }

    /**
     * Returns the parsed URL, including an always "non-www." version of the host.
     *
     * @param string $url
     * @return array|null
     */
    private function parseUrl($url)
    {
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['host'])) {
            return null;
        }

        $parsedUrl['non_www_host'] = ltrim($parsedUrl['host'], 'www.');

        return $parsedUrl;
    }
}
