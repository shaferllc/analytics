<?php

namespace Shaferllc\Analytics\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Shaferllc\Analytics\Models\Stat;
use Shaferllc\Analytics\Models\Recent;
use Shaferllc\Analytics\Models\Website;

trait StatTrait
{
    private function getOldTrafficSum(Website $site, array $range, string $metric)
    {
        return Stat::where('website_id', $site->id)
            ->where('name', $metric)
            ->whereBetween('date', [$range['from_old'], $range['to_old']])
            ->sum('count');
    }

    private function getTopItems(Website $site, array $range, string $metric, int $limit = 5)
    {
        $method = 'get' . ucfirst($metric) . 's';
        return $this->$method($site, $range, null, null, 'count', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getSumForStat($siteId, $statName, $range)
    {
        return Stat::where('website_id', $siteId)
            ->where('name', $statName)
            ->whereBetween('date', [$range['from'], $range['to']])
            ->sum('count');
    }

    private function getTotalReferrers(Website $site, array $range)
    {
        return Stat::where('website_id', $site->id)
            ->where('name', 'referrer')
            ->whereBetween('date', [$range['from'], $range['to']])
            ->sum('count');
    }

    private function getOverviewStats(Website $site, array $range)
    {
        $visitorsMap = $this->getTraffic($site, $range, 'visitors');
        $pageviewsMap = $this->getTraffic($site, $range, 'pageviews');

        return [
            'visitorsMap' => $visitorsMap,
            'pageviewsMap' => $pageviewsMap,
            'totalVisitors' => array_sum($visitorsMap),
            'totalPageviews' => array_sum($pageviewsMap),
            'totalVisitorsOld' => $this->getOldTrafficSum($site, $range, 'visitors'),
            'totalPageviewsOld' => $this->getOldTrafficSum($site, $range, 'pageviews'),
            'pages' => $this->getTopItems($site, $range, 'page'),
            'referrers' => $this->getTopItems($site, $range, 'referrer'),
            'countries' => $this->getTopItems($site, $range, 'country'),
            'browsers' => $this->getTopItems($site, $range, 'browser'),
            'operatingSystems' => $this->getTopItems($site, $range, 'os'),
            'events' => $this->getTopItems($site, $range, 'event'),
            'totalReferrers' => $this->getTotalReferrers($site, $range),
        ];
    }

    private function getRealtimeJsonResponse(Website $site)
    {
        $to = Carbon::now();
        $from = $to->copy()->subMinute();
        $to_old = $from->copy()->subSecond();
        $from_old = $to_old->copy()->subMinute();

        $realtimeData = $this->getRealtimeData($site, $from, $to, $from_old, $to_old);

        return response()->json([
            'visitors' => $realtimeData['visitorsCount'],
            'pageviews' => $realtimeData['pageviewsCount'],
            'visitors_growth' => view('stats.growth', ['growthCurrent' => $realtimeData['totalVisitors'], 'growthPrevious' => $realtimeData['visitorsOld']])->render(),
            'pageviews_growth' => view('stats.growth', ['growthCurrent' => $realtimeData['totalPageviews'], 'growthPrevious' => $realtimeData['pageviewsOld']])->render(),
            'recent' => view('stats.recent', ['website' => $site, 'range' => $this->range(), 'recent' => $realtimeData['recent']])->render(),
            'status' => 200
        ], 200);
    }

    private function getRealtimeData(Website $site, Carbon $from, Carbon $to, Carbon $from_old, Carbon $to_old)
    {
        $visitorsMap = $pageviewsMap = $this->calcAllDates($from, $to, 'second', 'Y-m-d H:i:s', ['count' => 0]);

        $visitors = $this->getRecentCount($site, $from, $to, true);
        $pageviews = $this->getRecentCount($site, $from, $to);

        $recent = Recent::where('website_id', $site->id)
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        $visitorsOld = $this->getRecentCount($site, $from_old, $to_old, true);
        $pageviewsOld = $this->getRecentCount($site, $from_old, $to_old);

        $totalVisitors = $totalPageviews = 0;

        foreach ($visitors as $visitor) {
            $visitorsMap[$visitor->created_at] = $visitor->count;
            $totalVisitors += $visitor->count;
        }

        foreach ($pageviews as $pageview) {
            $pageviewsMap[$pageview->created_at] = $pageview->count;
            $totalPageviews += $pageview->count;
        }

        $visitorsCount = $this->formatRealtimeCounts($visitorsMap);
        $pageviewsCount = $this->formatRealtimeCounts($pageviewsMap);

        return compact('visitorsCount', 'pageviewsCount', 'totalVisitors', 'totalPageviews', 'visitorsOld', 'pageviewsOld', 'recent');
    }

    private function getRecentCount(Website $site, Carbon $from, Carbon $to, bool $visitors = false)
    {
        return Recent::selectRaw('COUNT(`website_id`) as `count`, `created_at`')
            ->where('website_id', $site->id)
            ->whereBetween('created_at', [$from, $to])
            ->when($visitors, function ($query) use ($site) {
                $query->where(function ($q) use ($site) {
                    $q->where('referrer', '<>', $site->domain)
                        ->orWhereNull('referrer');
                });
            })
            ->groupBy('created_at')
            ->get();
    }

    private function getStatName($type)
    {
        return in_array($type, ['campaigns', 'continents']) ? $type : 'referrer';
    }

    private function getTotalStats($site, $range, $statName, $sites = null)
    {
        return Stat::where('website_id', $site->id)
            ->where('name', $statName)
            ->whereBetween('date', [$range['from'], $range['to']])
            ->when($sites, function ($query) use ($sites) {
                $query->whereIn('value', $sites);
            })
            ->sum('count');
    }

    private function getData($site, $range, $search, $searchBy, $sortBy, $sort, $type)
    {
        $methodMap = [
            'social-networks' => 'getSocialNetworks',
            'campaigns' => 'getCampaigns',
            'continents' => 'getContinents'
        ];

        if (!isset($methodMap[$type])) {
            throw new \InvalidArgumentException("Invalid type: {$type}");
        }

        return $this->{$methodMap[$type]}($site, $range, $search, $searchBy, $sortBy, $sort);
    }

    private function getStatData($site, $range, $statType, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return Stat::selectRaw('`value`, SUM(`count`) as `count`')
            ->where([
                ['website_id', '=', $site->id],
                ['name', '=', $statType]
            ])
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchValue($search);
            })
            ->whereBetween('date', [$range['from'], $range['to']])
            ->groupBy('value')
            ->orderBy($sortBy ?? 'count', $sort ?? 'desc');
    }

    private function getCampaigns($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'campaign', $search, $searchBy, $sortBy, $sort);
    }

    private function getContinents($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'continent', $search, $searchBy, $sortBy, $sort);
    }

    private function getCountries($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'country', $search, $searchBy, $sortBy, $sort);
    }

    private function getCities($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'city', $search, $searchBy, $sortBy, $sort);
    }

    private function getLanguages($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'language', $search, $searchBy, $sortBy, $sort);
    }

    private function getOperatingSystems($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'os', $search, $searchBy, $sortBy, $sort);
    }

    private function getBrowsers($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'browser', $search, $searchBy, $sortBy, $sort);
    }

    private function getScreenResolutions($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'resolution', $search, $searchBy, $sortBy, $sort);
    }

    private function getDevices($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'device', $search, $searchBy, $sortBy, $sort);
    }

    private function getEvents($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($site, $range, 'event', $search, $searchBy, $sortBy, $sort);
    }

    private function getTraffic($site, $range, $type)
    {
        return $range['unit'] == 'hour'
            ? $this->getHourlyTraffic($site, $range, $type)
            : $this->getDailyTraffic($site, $range, $type);
    }

    private function getHourlyTraffic($site, $range, $type)
    {
        $rows = Stat::where([
                ['website_id', '=', $site->id],
                ['name', '=', $type . '_hours']
            ])
            ->whereBetween('date', [$range['from'], $range['to']])
            ->orderBy('date', 'asc')
            ->get();

        $output = array_fill_keys(range('00', '23'), 0);

        foreach ($rows as $row) {
            $output[$row->value] = $row->count;
        }

        return $output;
    }

    private function getDailyTraffic($site, $range, $type)
    {
        $rows = Stat::select([
                DB::raw("date_format(`date`, '". str_replace(['Y', 'm', 'd'], ['%Y', '%m', '%d'], $range['format'])."') as `date_result`, SUM(`count`) as `aggregate`")
            ])
            ->where([['website_id', '=', $site->id], ['name', '=', $type]])
            ->whereBetween('date', [$range['from'], $range['to']])
            ->groupBy('date_result')
            ->orderBy('date_result', 'asc')
            ->get();

        $rangeMap = $this->calcAllDates(
            Carbon::createFromFormat('Y-m-d', $range['from'])->format($range['format']),
            Carbon::createFromFormat('Y-m-d', $range['to'])->format($range['format']),
            $range['unit'],
            $range['format'],
            0
        );

        $collection = $rows->mapWithKeys(function ($result) use ($range) {
            $key = $range['unit'] == 'year' ? $result->date_result : Carbon::parse($result->date_result)->format($range['format']);
            return [strval($key) => $result->aggregate];
        })->all();

        return array_replace($rangeMap, $collection);
    }

    private function getSocialNetworksList()
    {
        return [
            'l.facebook.com', 't.co', 'l.instagram.com', 'out.reddit.com',
            'www.youtube.com', 'away.vk.com', 't.umblr.com', 'www.pinterest.com',
            'www.linkedin.com', 'www.x.com', 'www.tiktok.com', 'www.reddit.com', 'www.snapchat.com', 'www.whatsapp.com', 'www.telegram.org',
            'www.weibo.com', 'www.quora.com', 'www.medium.com', 'www.flickr.com', 'www.vimeo.com', 'www.twitch.tv',
            'www.discord.com', 'www.clubhouse.com', 'www.meetup.com',
            'www.tumblr.com', 'www.mastodon.social', 'www.threads.net', 'www.goodreads.com',
            'www.behance.net', 'www.deviantart.com', 'www.dribbble.com', 'www.last.fm',
            'www.soundcloud.com', 'www.spotify.com', 'www.myspace.com', 'www.tagged.com',
            'www.nextdoor.com', 'www.xing.com', 'www.periscope.tv', 'www.foursquare.com',
            'www.slideshare.net', 'www.academia.edu', 'www.researchgate.net', 'www.yelp.com',
            'www.tripadvisor.com', 'www.strava.com', 'www.fitbit.com', 'www.untappd.com'
        ];
    }

    private function getSearchEnginesList()
    {
        return [
            'www.google.com', 'www.bing.com', 'search.yahoo.com', 'uk.search.yahoo.com',
            'de.search.yahoo.com', 'fr.search.yahoo.com', 'es.search.yahoo.com',
            'search.aol.co.uk', 'search.aol.com', 'duckduckgo.com', 'www.baidu.com',
            'yandex.ru', 'www.ecosia.org', 'search.lycos.com', 'www.ask.com',
            'www.qwant.com', 'www.startpage.com', 'www.dogpile.com', 'www.wolframalpha.com',
            'www.boardreader.com', 'www.gibiru.com', 'www.searchencrypt.com',
            'www.yippy.com', 'www.swisscows.com', 'www.mojeek.com', 'www.metacrawler.com',
            'www.search.brave.com', 'www.gigablast.com', 'www.entireweb.com',
            'www.info.com', 'www.oscobo.com', 'www.lukol.com', 'www.disconnect.me',
            'www.metager.org', 'www.searx.me', 'www.peekier.com'
        ];
    }

    private function getPages($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return Stat::selectRaw('`value`, SUM(`count`) as `count`')
            ->where([['website_id', '=', $site->id], ['name', '=', 'page']])
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchValue($search);
            })
            ->whereBetween('date', [$range['from'], $range['to']])
            ->groupBy('value')
            ->orderBy($sortBy, $sort);
    }

    private function getReferrers($site, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return Stat::selectRaw('`value`, SUM(`count`) as `count`')
            ->where([['website_id', '=', $site->id], ['name', '=', 'referrer'], ['value', '<>', $site->domain], ['value', '<>', '']])
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchValue($search);
            })
            ->whereBetween('date', [$range['from'], $range['to']])
            ->groupBy('value')
            ->orderBy($sortBy, $sort);
    }

}
