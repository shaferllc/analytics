<?php

namespace Shaferllc\Analytics\Http\Controllers;

use Carbon\Carbon;
use League\Csv as CSV;
use Illuminate\View\View;
use Carbon\CarbonTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Shaferllc\Analytics\Models\Stat;
use Shaferllc\Analytics\Models\Recent;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Shaferllc\Analytics\Http\Requests\ValidateWebsitePasswordRequest;


class StatController extends BaseController
{
    use DateRangeTrait;

    /**
     * Show the Overview stats page.
     */
    public function index(Request $request, Website $website)
    {

        if ($this->guard($website)) {
            return view('analytics::stats.password', ['website' => $website]);
        }

        
        $now = Carbon::now();
        $range = $this->range();

        $visitorsMap = $this->getStats($website, $range, 'visitors');
        
        $pageviewsMap = $this->getStats($website, $range, 'pageviews');

        $totalVisitors = $totalPageviews = 0;
        foreach ($visitorsMap as $key => $value) {
            $totalVisitors = $totalVisitors + $value;
        }

        foreach ($pageviewsMap as $key => $value) {
            $totalPageviews = $totalPageviews + $value;
        }

        
        $totalVisitorsOld = Stat::where([['website_id', '=', $website->id], ['name', '=', 'visitors']])
            ->whereBetween('date', [$range['from_old'], $range['to_old']])
            ->sum('count');

        $totalPageviewsOld = Stat::where([['website_id', '=', $website->id], ['name', '=', 'pageviews']])
            ->whereBetween('date', [$range['from_old'], $range['to_old']])
            ->sum('count');

        $pages = $this->getPages($website, $range, [], 'count', 'desc')
            ->limit(5)
            ->get();

        $totalReferrers = Stat::where([['website_id', '=', $website->id], ['name', '=', 'referrer']])
            ->whereBetween('date', [$range['from'], $range['to']])
            ->sum('count');

        $referrers = $this->getReferrers($website, $range, null, null, 'count', 'desc')
            ->limit(5)
            ->get();

        $countries = $this->getCountries($website, $range, null, null, 'count', 'desc')
            ->limit(5)
            ->get();

        $browsers = $this->getBrowsers($website, $range, null, null, 'count', 'desc')
            ->limit(5)
            ->get();

        $operatingSystems = $this->getOperatingSystems($website, $range, null, null, 'count', 'desc')
            ->limit(5)
            ->get();

        $events = $this->getEvents($website, $range, null, null, 'count', 'desc')
            ->limit(5)
            ->get();

        return view('analytics::stats.container', ['view' => 'overview', 'website' => $website, 'now' => $now, 'range' => $range, 'referrers' => $referrers, 'pages' => $pages, 'visitorsMap' => $visitorsMap, 'pageviewsMap' => $pageviewsMap, 'countries' => $countries, 'browsers' => $browsers, 'operatingSystems' => $operatingSystems, 'events' => $events, 'totalVisitors' => $totalVisitors, 'totalPageviews' => $totalPageviews, 'totalVisitorsOld' => $totalVisitorsOld, 'totalPageviewsOld' => $totalPageviewsOld, 'totalReferrers' => $totalReferrers]);
    }
   

    /**
     * Show the Operating Systems stats page.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function operatingSystems(Request $request, $id)
    {
        $website = Website::where('domain', $id)->firstOrFail();

        if ($this->guard($website)) {
            return view('stats.password', ['website' => $website]);
        };

        $now = Carbon::now();
        $range = $this->range();
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['value']) ? $request->input('search_by') : 'value';
        $sortBy = in_array($request->input('sort_by'), ['count', 'value']) ? $request->input('sort_by') : 'count';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $total = Stat::selectRaw('SUM(`count`) as `count`')
            ->where([['website_id', '=', $website->id], ['name', '=', 'os']])
            ->whereBetween('date', [$range['from'], $range['to']])
            ->first();

        $operatingSystems = $this->getOperatingSystems($website, $range, $search, $searchBy, $sortBy, $sort)
            ->paginate($perPage)
            ->appends(['from' => $range['from'], 'to' => $range['to'], 'search' => $search, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        $first = $this->getOperatingSystems($website, $range, $search, $searchBy, 'count', 'desc')
            ->first();

        $last = $this->getOperatingSystems($website, $range, $search, $searchBy, 'count', 'asc')
            ->first();

        return view('stats.container', ['view' => 'operating-systems', 'website' => $website, 'now' => $now, 'range' => $range, 'export' => 'stats.export.operating_systems', 'operatingSystems' => $operatingSystems, 'first' => $first, 'last' => $last, 'total' => $total]);
    }

    /**
     * Show the Browsers stats page.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function browsers(Request $request, $id)
    {
        $website = Website::where('domain', $id)->firstOrFail();

        if ($this->guard($website)) {
            return view('stats.password', ['website' => $website]);
        };

        $now = Carbon::now();
        $range = $this->range();
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['value']) ? $request->input('search_by') : 'value';
        $sortBy = in_array($request->input('sort_by'), ['count', 'value']) ? $request->input('sort_by') : 'count';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $total = Stat::selectRaw('SUM(`count`) as `count`')
            ->where([['website_id', '=', $website->id], ['name', '=', 'browser']])
            ->whereBetween('date', [$range['from'], $range['to']])
            ->first();

        $browsers = $this->getBrowsers($website, $range, $search, $searchBy, $sortBy, $sort)
            ->paginate($perPage)
            ->appends(['from' => $range['from'], 'to' => $range['to'], 'search' => $search, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        $first = $this->getBrowsers($website, $range, $search, $searchBy, 'count', 'desc')
            ->first();

        $last = $this->getBrowsers($website, $range, $search, $searchBy, 'count', 'asc')
            ->first();

        return view('stats.container', ['view' => 'browsers', 'website' => $website, 'now' => $now, 'range' => $range, 'export' => 'stats.export.browsers', 'browsers' => $browsers, 'first' => $first, 'last' => $last, 'total' => $total]);
    }


    /**
     * Get the Referrers.
     *
     * @param $website
     * @param $range
     * @param null $search
     * @param null $sort
     * @return mixed
     */
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

    


    /**
     * Get the Browsers.
     *
     * @param $website
     * @param $range
     * @param null $search
     * @param null $sort
     * @return mixed
     */
    private function getBrowsers($website, $range, $search = null, $searchBy = null, $sortBy = null, $sort = null)
    {
        return Stat::selectRaw('`value`, SUM(`count`) as `count`')
            ->where([['website_id', '=', $website->id], ['name', '=', 'browser']])
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchValue($search);
            })
            ->whereBetween('date', [$range['from'], $range['to']])
            ->groupBy('value')
            ->orderBy($sortBy, $sort);
    }



    /**
     * Get the stats in a formatted way, based on the date range.
     *
     * @param $website
     * @param $range
     * @param $type
     * @return array|int[]
     */
   



    /**
     * List of Search Engine domains.
     *
     * @return string[]
     */
    private function getSearchEnginesList()
    {
        return ['www.google.com', 'www.google.com', 'www.google.ad', 'www.google.ae', 'www.google.com.af', 'www.google.com.ag', 'www.google.com.ai', 'www.google.al', 'www.google.am', 'www.google.co.ao', 'www.google.com.ar', 'www.google.as', 'www.google.at', 'www.google.com.au', 'www.google.az', 'www.google.ba', 'www.google.com.bd', 'www.google.be', 'www.google.bf', 'www.google.bg', 'www.google.com.bh', 'www.google.bi', 'www.google.bj', 'www.google.com.bn', 'www.google.com.bo', 'www.google.com.br', 'www.google.bs', 'www.google.bt', 'www.google.co.bw', 'www.google.by', 'www.google.com.bz', 'www.google.ca', 'www.google.cd', 'www.google.cf', 'www.google.cg', 'www.google.ch', 'www.google.ci', 'www.google.co.ck', 'www.google.cl', 'www.google.cm', 'www.google.cn', 'www.google.com.co', 'www.google.co.cr', 'www.google.com.cu', 'www.google.cv', 'www.google.com.cy', 'www.google.cz', 'www.google.de', 'www.google.dj', 'www.google.dk', 'www.google.dm', 'www.google.com.do', 'www.google.dz', 'www.google.com.ec', 'www.google.ee', 'www.google.com.eg', 'www.google.es', 'www.google.com.et', 'www.google.fi', 'www.google.com.fj', 'www.google.fm', 'www.google.fr', 'www.google.ga', 'www.google.ge', 'www.google.gg', 'www.google.com.gh', 'www.google.com.gi', 'www.google.gl', 'www.google.gm', 'www.google.gr', 'www.google.com.gt', 'www.google.gy', 'www.google.com.hk', 'www.google.hn', 'www.google.hr', 'www.google.ht', 'www.google.hu', 'www.google.co.id', 'www.google.ie', 'www.google.co.il', 'www.google.im', 'www.google.co.in', 'www.google.iq', 'www.google.is', 'www.google.it', 'www.google.je', 'www.google.com.jm', 'www.google.jo', 'www.google.co.jp', 'www.google.co.ke', 'www.google.com.kh', 'www.google.ki', 'www.google.kg', 'www.google.co.kr', 'www.google.com.kw', 'www.google.kz', 'www.google.la', 'www.google.com.lb', 'www.google.li', 'www.google.lk', 'www.google.co.ls', 'www.google.lt', 'www.google.lu', 'www.google.lv', 'www.google.com.ly', 'www.google.co.ma', 'www.google.md', 'www.google.me', 'www.google.mg', 'www.google.mk', 'www.google.ml', 'www.google.com.mm', 'www.google.mn', 'www.google.ms', 'www.google.com.mt', 'www.google.mu', 'www.google.mv', 'www.google.mw', 'www.google.com.mx', 'www.google.com.my', 'www.google.co.mz', 'www.google.com.na', 'www.google.com.ng', 'www.google.com.ni', 'www.google.ne', 'www.google.nl', 'www.google.no', 'www.google.com.np', 'www.google.nr', 'www.google.nu', 'www.google.co.nz', 'www.google.com.om', 'www.google.com.pa', 'www.google.com.pe', 'www.google.com.pg', 'www.google.com.ph', 'www.google.com.pk', 'www.google.pl', 'www.google.pn', 'www.google.com.pr', 'www.google.ps', 'www.google.pt', 'www.google.com.py', 'www.google.com.qa', 'www.google.ro', 'www.google.ru', 'www.google.rw', 'www.google.com.sa', 'www.google.com.sb', 'www.google.sc', 'www.google.se', 'www.google.com.sg', 'www.google.sh', 'www.google.si', 'www.google.sk', 'www.google.com.sl', 'www.google.sn', 'www.google.so', 'www.google.sm', 'www.google.sr', 'www.google.st', 'www.google.com.sv', 'www.google.td', 'www.google.tg', 'www.google.co.th', 'www.google.com.tj', 'www.google.tl', 'www.google.tm', 'www.google.tn', 'www.google.to', 'www.google.com.tr', 'www.google.tt', 'www.google.com.tw', 'www.google.co.tz', 'www.google.com.ua', 'www.google.co.ug', 'www.google.co.uk', 'www.google.com.uy', 'www.google.co.uz', 'www.google.com.vc', 'www.google.co.ve', 'www.google.vg', 'www.google.co.vi', 'www.google.com.vn', 'www.google.vu', 'www.google.ws', 'www.google.rs', 'www.google.co.za', 'www.google.co.zm', 'www.google.co.zw', 'www.google.cat', 'www.bing.com', 'search.yahoo.com', 'uk.search.yahoo.com', 'de.search.yahoo.com', 'fr.search.yahoo.com', 'es.search.yahoo.com', 'search.aol.co.uk', 'search.aol.com', 'duckduckgo.com', 'www.baidu.com', 'yandex.ru', 'www.ecosia.org', 'search.lycos.com'];
    }

  

  
}
