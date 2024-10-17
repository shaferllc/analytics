<?php

namespace ShaferLLC\Analytics\Http\Controllers\API;

use ShaferLLC\Analytics\Http\Controllers\Controller;
use GeoIp2\Database\Reader as GeoIP;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\IpUtils;
use WhichBrowser\Parser as UserAgent;

class EventController extends Controller
{
    /**
     * The tracking mechanism.
     *
     * @param Request $request
     * @return int|void
     */
    public function index(Request $request)
    {
        $page = $this->parseUrl($request->input('page'));
        $website = $this->getWebsite($page['non_www_host'] ?? null);

        if (!$website || !$website->can_track || $this->isExcludedIp($website, $request->ip())) {
            return 403;
        }

        $ua = new UserAgent(getallheaders());
        if ($website->exclude_bots && $ua->device->type == 'bot') {
            return 403;
        }

        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H');

        $data = $request->input('event') 
            ? $this->handleEvent($request)
            : $this->handlePageview($request, $website, $page, $ua, $date, $time);

        if (empty($data)) {
            return;
        }

        $this->saveData($website, $data, $date, $now);

        return 200;
    }

    private function getWebsite($domain)
    {
        return DB::table('websites')
            ->select(['websites.id', 'websites.domain', 'websites.user_id', 'websites.exclude_bots', 'websites.exclude_ips', 'websites.exclude_params', 'users.can_track'])
            ->join('users', 'users.id', '=', 'websites.user_id')
            ->where('websites.domain', '=', $domain)
            ->first();
    }

    private function isExcludedIp($website, $ip)
    {
        if ($website->exclude_ips) {
            $excludedIps = preg_split('/\n|\r/', $website->exclude_ips, -1, PREG_SPLIT_NO_EMPTY);
            return IpUtils::checkIp($ip, $excludedIps);
        }
        return false;
    }

    private function handleEvent(Request $request)
    {
        $event = $request->input('event');
        if (!isset($event['name'])) {
            return [];
        }

        $eventName = str_replace(':', ' ', $event['name']);
        $eventValue = isset($event['value']) && is_numeric($event['value']) && $event['value'] > 0 && strlen($event['value']) <= 10 ? trim($event['value']) : null;
        $eventUnit = isset($event['unit']) && mb_strlen($event['unit']) <= 32 ? $event['unit'] : null;

        return ['event' => implode(':', [$eventName, $eventValue, $eventUnit])];
    }

    private function handlePageview(Request $request, $website, $page, $ua, $date, $time)
    {
        $referrer = $this->parseUrl($request->input('referrer'));
        $data = [
            'pageviews' => $date,
            'pageviews_hours' => $time,
        ];

        $this->handleExcludedParams($website, $page);

        $data['page'] = mb_substr((isset($page['query']) && !empty($page['query']) ? $page['path'].'?'.$page['query'] : $page['path'] ?? '/'), 0, 255);

        $geoData = $this->getGeoData($request->ip());
        $browserData = $this->getBrowserData($ua);

        if (!isset($referrer['non_www_host']) || $referrer['non_www_host'] != $website->domain) {
            $this->addUniqueVisitorData($data, $page, $referrer, $geoData, $browserData, $request, $date, $time);
        }

        return $data;
    }

    private function handleExcludedParams($website, &$page)
    {
        if ($website->exclude_params) {
            parse_str($page['query'] ?? null, $params);
            $excludeQueries = preg_split('/\n|\r/', $website->exclude_params, -1, PREG_SPLIT_NO_EMPTY);

            if (in_array('&', $excludeQueries)) {
                $page['query'] = null;
            } else {
                $page['query'] = http_build_query(array_diff_key($params, array_flip($excludeQueries)));
            }
        }
    }

    private function getGeoData($ip)
    {
        try {
            $geoip = (new GeoIP(storage_path('app/geoip/GeoLite2-City.mmdb')))->city($ip);
            return [
                'continent' => $geoip->continent->code.':'.$geoip->continent->name,
                'country' => $geoip->country->isoCode.':'.$geoip->country->name,
                'city' => $geoip->country->isoCode.':'. $geoip->city->name .(isset($geoip->mostSpecificSubdivision->isoCode) ? ', '.$geoip->mostSpecificSubdivision->isoCode : '')
            ];
        } catch (\Exception $e) {
            return ['continent' => null, 'country' => null, 'city' => null];
        }
    }

    private function getBrowserData($ua)
    {
        return [
            'browser' => mb_substr($ua->browser->name ?? null, 0, 64),
            'os' => mb_substr($ua->os->name ?? null, 0, 64),
            'device' => mb_substr($ua->device->type ?? null, 0, 64),
        ];
    }

    private function addUniqueVisitorData(array &$data, $page, $referrer, $geoData, $browserData, $request, $date, $time)
    {
        parse_str($page['query'] ?? null, $params);
        if (!empty($params['utm_campaign'])) {
            $data['campaign'] = $params['utm_campaign'];
        }

        $data = array_merge($data, $geoData, $browserData, [
            'language' => mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2) ?: null,
            'visitors' => $date,
            'visitors_hours' => $time,
            'resolution' => $request->input('screen_resolution'),
            'landing_page' => $data['page'],
            'referrer' => mb_substr($referrer['host'] ?? null, 0, 255),
        ]);
    }

    private function saveData($website, $data, $date, $now)
    {
        $values = [];
        foreach ($data as $name => $value) {
            $values[] = "({$website->id}, '{$name}', " . DB::connection()->getPdo()->quote(mb_substr($value, 0, 255)) . ", '{$date}')";
        }

        DB::statement("INSERT INTO `stats` (`website_id`, `name`, `value`, `date`) VALUES " . implode(', ', $values) . " ON DUPLICATE KEY UPDATE `count` = `count` + 1;");

        if (empty($data['event'])) {
            DB::statement("INSERT INTO `recents` (`id`, `website_id`, `page`, `referrer`, `os`, `browser`, `device`, `country`, `city`, `language`, `created_at`) VALUES (NULL, :website_id, :page, :referrer, :os, :browser, :device, :country, :city, :language, :timestamp)", [
                'website_id' => $website->id,
                'page' => $data['page'],
                'referrer' => $data['referrer'] ?? null,
                'os' => $data['os'] ?? null,
                'browser' => $data['browser'] ?? null,
                'device' => $data['device'] ?? null,
                'country' => $data['country'] ?? null,
                'city' => $data['city'] ?? null,
                'language' => $data['language'] ?? null,
                'timestamp' => $now
            ]);
        }
    }

    /**
     * Returns the parsed URL, including an always "non-www." version of the host.
     *
     * @param $url
     * @return mixed|null
     */
    private function parseUrl($url)
    {
        $parsed = parse_url($url);
        if (isset($parsed['host'])) {
            $parsed['non_www_host'] = preg_replace('/^www\./', '', $parsed['host']);
            return $parsed;
        }
        return null;
    }
}
