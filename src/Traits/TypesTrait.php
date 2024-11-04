<?php

namespace Shaferllc\Analytics\Traits;

use Illuminate\Http\Request;
use Shaferllc\Analytics\Models\Stat;
use Shaferllc\Analytics\Models\Website;

trait TypesTrait {

    private function prepareCommonData(Request $request, $id)
    {
        $website = Website::where('domain', $id)->firstOrFail();

        if ($this->statsGuard($website)) {
            return ['view' => 'stats.password', 'website' => $website];
        }

        $range = $this->range();
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['value']) ? $request->input('search_by') : 'value';
        $sortBy = in_array($request->input('sort_by'), ['count', 'value']) ? $request->input('sort_by') : 'count';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        return compact('website', 'range', 'search', 'searchBy', 'sortBy', 'sort', 'perPage');
    }

    private function getTotal($website, $range, $name)
    {
        return Stat::selectRaw('SUM(`count`) as `count`')
            ->where([['website_id', '=', $website->id], ['name', '=', $name]])
            ->whereBetween('date', [$range['from'], $range['to']])
            ->first();
    }

    private function getPaginatedData($method, $website, $range, $search, $searchBy, $sortBy, $sort, $perPage)
    {
        $data = $this->$method($website, $range, $search, $searchBy, $sortBy, $sort)
            ->paginate($perPage)
            ->appends(['from' => $range['from'], 'to' => $range['to'], 'search' => $search, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort]);

        $first = $this->$method($website, $range, $search, $searchBy, 'count', 'desc')->first();
        $last = $this->$method($website, $range, $search, $searchBy, 'count', 'asc')->first();

        return compact('data', 'first', 'last');
    }

    private function renderView($view, $data)
    {
        return view('stats.container', array_merge(['view' => $view], $data));
    }

    public function pages(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'page');
        $paginatedData = $this->getPaginatedData('getPages', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('pages', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.pages',
            'pages' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function landingPages(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'landing_page');
        $paginatedData = $this->getPaginatedData('getLandingPages', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('landing-pages', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.landing_pages',
            'landingPages' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function referrers(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = Stat::selectRaw('SUM(`count`) as `count`')
            ->where([['website_id', '=', $website->id], ['name', '=', 'referrer'], ['value', '<>', $website->domain], ['value', '<>', '']])
            ->whereBetween('date', [$range['from'], $range['to']])
            ->first();
        $paginatedData = $this->getPaginatedData('getReferrers', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('referrers', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.referrers',
            'referrers' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function searchEngines(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $websites = $this->getSearchEnginesList();
        $total = Stat::selectRaw('SUM(`count`) as `count`')
            ->where([['website_id', '=', $website->id], ['name', '=', 'referrer']])
            ->whereIn('value', $websites)
            ->whereBetween('date', [$range['from'], $range['to']])
            ->first();
        $paginatedData = $this->getPaginatedData('getSearchEngines', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('search-engines', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.search_engines',
            'searchEngines' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }
    
    public function countries(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'country');
        $paginatedData = $this->getPaginatedData('getCountries', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);
        $countriesChart = $this->getCountries($website, $range, $search, $searchBy, $sortBy, $sort)->get();

        return $this->renderView('countries', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.countries',
            'countries' => $paginatedData['data'],
            'countriesChart' => $countriesChart,
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function cities(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'city');
        $paginatedData = $this->getPaginatedData('getCities', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('cities', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.cities',
            'cities' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function languages(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'language');
        $paginatedData = $this->getPaginatedData('getLanguages', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('languages', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.languages',
            'languages' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function operatingSystems(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'os');
        $paginatedData = $this->getPaginatedData('getOperatingSystems', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('operating-systems', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.operating_systems',
            'operatingSystems' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function browsers(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'browser');
        $paginatedData = $this->getPaginatedData('getBrowsers', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('browsers', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.browsers',
            'browsers' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function screenResolutions(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'resolution');
        $paginatedData = $this->getPaginatedData('getScreenResolutions', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('screen-resolutions', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.screen_resolutions',
            'screenResolutions' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function devices(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'device');
        $paginatedData = $this->getPaginatedData('getDevices', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('devices', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.devices',
            'devices' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }

    public function events(Request $request, $id)
    {
        $commonData = $this->prepareCommonData($request, $id);
        if (isset($commonData['view'])) return view($commonData['view'], $commonData);

        extract($commonData);
        $total = $this->getTotal($website, $range, 'event');
        $paginatedData = $this->getPaginatedData('getEvents', $website, $range, $search, $searchBy, $sortBy, $sort, $perPage);

        return $this->renderView('events', [
            'website' => $website,
            'range' => $range,
            'export' => 'stats.export.events',
            'events' => $paginatedData['data'],
            'first' => $paginatedData['first'],
            'last' => $paginatedData['last'],
            'total' => $total
        ]);
    }
}