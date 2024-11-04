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
    private function getOldTrafficSum(Website $website, array $range, string $metric)
    {
        return Stat::where('website_id', $website->id)
            ->where('name', $metric)
            ->whereBetween('date', [$range['from_old'], $range['to_old']])
            ->sum('count');
    }

    private function getTopItems(Website $website, array $range, string $metric, int $limit = 5)
    {
        $method = 'get' . ucfirst($metric) . 's';
        return $this->$method($website, $range, null, null, 'count', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getSumForStat($websiteId, $statName, $range)
    {
        return Stat::where('website_id', $websiteId)
            ->where('name', $statName)
            ->whereBetween('date', [$range['from'], $range['to']])
            ->sum('count');
    }
    
    private function getTotalReferrers(Website $website, array $range)
    {
        return Stat::where('website_id', $website->id)
            ->where('name', 'referrer')
            ->whereBetween('date', [$range['from'], $range['to']])
            ->sum('count');
    }

    private function getOverviewStats(Website $website, array $range)
    {
        $visitorsMap = $this->getTraffic($website, $range, 'visitors');
        $pageviewsMap = $this->getTraffic($website, $range, 'pageviews');

        return [
            'visitorsMap' => $visitorsMap,
            'pageviewsMap' => $pageviewsMap,
            'totalVisitors' => array_sum($visitorsMap),
            'totalPageviews' => array_sum($pageviewsMap),
            'totalVisitorsOld' => $this->getOldTrafficSum($website, $range, 'visitors'),
            'totalPageviewsOld' => $this->getOldTrafficSum($website, $range, 'pageviews'),
            'pages' => $this->getTopItems($website, $range, 'page'),
            'referrers' => $this->getTopItems($website, $range, 'referrer'),
            'countries' => $this->getTopItems($website, $range, 'country'),
            'browsers' => $this->getTopItems($website, $range, 'browser'),
            'operatingSystems' => $this->getTopItems($website, $range, 'os'),
            'events' => $this->getTopItems($website, $range, 'event'),
            'totalReferrers' => $this->getTotalReferrers($website, $range),
        ];
    }

    private function getRealtimeJsonResponse(Website $website)
    {
        $to = Carbon::now();
        $from = $to->copy()->subMinute();
        $to_old = $from->copy()->subSecond();
        $from_old = $to_old->copy()->subMinute();

        $realtimeData = $this->getRealtimeData($website, $from, $to, $from_old, $to_old);

        return response()->json([
            'visitors' => $realtimeData['visitorsCount'],
            'pageviews' => $realtimeData['pageviewsCount'],
            'visitors_growth' => view('stats.growth', ['growthCurrent' => $realtimeData['totalVisitors'], 'growthPrevious' => $realtimeData['visitorsOld']])->render(),
            'pageviews_growth' => view('stats.growth', ['growthCurrent' => $realtimeData['totalPageviews'], 'growthPrevious' => $realtimeData['pageviewsOld']])->render(),
            'recent' => view('stats.recent', ['website' => $website, 'range' => $this->range(), 'recent' => $realtimeData['recent']])->render(),
            'status' => 200
        ], 200);
    }

    private function getRealtimeData(Website $website, Carbon $from, Carbon $to, Carbon $from_old, Carbon $to_old)
    {
        $visitorsMap = $pageviewsMap = $this->calcAllDates($from, $to, 'second', 'Y-m-d H:i:s', ['count' => 0]);

        $visitors = $this->getRecentCount($website, $from, $to, true);
        $pageviews = $this->getRecentCount($website, $from, $to);

        $recent = Recent::where('website_id', $website->id)
            ->whereBetween('created_at', [$from, $to])
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        $visitorsOld = $this->getRecentCount($website, $from_old, $to_old, true);
        $pageviewsOld = $this->getRecentCount($website, $from_old, $to_old);

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

    private function getRecentCount(Website $website, Carbon $from, Carbon $to, bool $visitors = false)
    {
        return Recent::selectRaw('COUNT(`website_id`) as `count`, `created_at`')
            ->where('website_id', $website->id)
            ->whereBetween('created_at', [$from, $to])
            ->when($visitors, function ($query) use ($website) {
                $query->where(function ($q) use ($website) {
                    $q->where('referrer', '<>', $website->domain)
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

    private function getTotalStats($website, $range, $statName, $websites = null)
    {
        return Stat::where('website_id', $website->id)
            ->where('name', $statName)
            ->whereBetween('date', [$range['from'], $range['to']])
            ->when($websites, function ($query) use ($websites) {
                $query->whereIn('value', $websites);
            })
            ->sum('count');
    }

    private function getData($website, $range, $search, $searchBy, $sortBy, $sort, $type)
    {
        $methodMap = [
            'social-networks' => 'getSocialNetworks',
            'campaigns' => 'getCampaigns',
            'continents' => 'getContinents'
        ];

        if (!isset($methodMap[$type])) {
            throw new \InvalidArgumentException("Invalid type: {$type}");
        }

        return $this->{$methodMap[$type]}($website, $range, $search, $searchBy, $sortBy, $sort);
    }

    private function getStatData($website, $range, $statType, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return Stat::selectRaw('`value`, SUM(`count`) as `count`')
            ->where([
                ['website_id', '=', $website->id],
                ['name', '=', $statType]
            ])
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchValue($search);
            })
            ->whereBetween('date', [$range['from'], $range['to']])
            ->groupBy('value')
            ->orderBy($sortBy ?? 'count', $sort ?? 'desc');
    }

    private function getCampaigns($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'campaign', $search, $searchBy, $sortBy, $sort);
    }

    private function getContinents($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'continent', $search, $searchBy, $sortBy, $sort);
    }

    private function getCountries($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'country', $search, $searchBy, $sortBy, $sort);
    }

    private function getCities($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'city', $search, $searchBy, $sortBy, $sort);
    }

    private function getLanguages($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'language', $search, $searchBy, $sortBy, $sort);
    }

    private function getOperatingSystems($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'os', $search, $searchBy, $sortBy, $sort);
    }

    private function getBrowsers($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'browser', $search, $searchBy, $sortBy, $sort);
    }

    private function getScreenResolutions($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'resolution', $search, $searchBy, $sortBy, $sort);
    }

    private function getDevices($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'device', $search, $searchBy, $sortBy, $sort);
    }

    private function getEvents($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return $this->getStatData($website, $range, 'event', $search, $searchBy, $sortBy, $sort);
    }

    private function getTraffic($website, $range, $type)
    {
        return $range['unit'] == 'hour' 
            ? $this->getHourlyTraffic($website, $range, $type)
            : $this->getDailyTraffic($website, $range, $type);
    }

    private function getHourlyTraffic($website, $range, $type)
    {
        $rows = Stat::where([
                ['website_id', '=', $website->id], 
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

    private function getDailyTraffic($website, $range, $type)
    {
        $rows = Stat::select([
                DB::raw("date_format(`date`, '". str_replace(['Y', 'm', 'd'], ['%Y', '%m', '%d'], $range['format'])."') as `date_result`, SUM(`count`) as `aggregate`")
            ])
            ->where([['website_id', '=', $website->id], ['name', '=', $type]])
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

    private function getPages($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return Stat::selectRaw('`value`, SUM(`count`) as `count`')
            ->where([['website_id', '=', $website->id], ['name', '=', 'page']])
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchValue($search);
            })
            ->whereBetween('date', [$range['from'], $range['to']])
            ->groupBy('value')
            ->orderBy($sortBy, $sort);
    }

    private function getReferrers($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return Stat::selectRaw('`value`, SUM(`count`) as `count`')
            ->where([['website_id', '=', $website->id], ['name', '=', 'referrer'], ['value', '<>', $website->domain], ['value', '<>', '']])
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchValue($search);
            })
            ->whereBetween('date', [$range['from'], $range['to']])
            ->groupBy('value')
            ->orderBy($sortBy, $sort);
    }

}
