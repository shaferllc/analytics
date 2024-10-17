<?php

namespace ShaferLLC\Analytics\Http\Controllers;

use ShaferLLC\Analytics\Models\Stat;
use ShaferLLC\Analytics\Traits\DateRangeTrait;
use ShaferLLC\Analytics\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use DateRangeTrait;

    /**
     * Show the Dashboard page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        $range = $this->range();
        $userId = $request->user()->id;

        $statsData = $this->getStatsData($userId, $range);

        $websitesQuery = $this->getWebsitesQuery($request, $userId, $range);

        $websites = $websitesQuery->paginate($this->getPerPage($request))
            ->appends($this->getQueryParams($request, $range));

        return view('dashboard.index', array_merge($statsData, [
            'range' => $range,
            'websites' => $websites
        ]));
    }

    private function getStatsData($userId, $range)
    {
        $websiteIds = Website::where('user_id', $userId)->pluck('id');

        $stats = Stat::whereIn('website_id', $websiteIds)
            ->whereIn('name', ['visitors', 'pageviews'])
            ->whereBetween('date', [$range['from_old'], $range['to']])
            ->groupBy('name')
            ->select('name', 
                DB::raw('SUM(CASE WHEN date BETWEEN ? AND ? THEN count ELSE 0 END) as current_count'),
                DB::raw('SUM(CASE WHEN date BETWEEN ? AND ? THEN count ELSE 0 END) as old_count')
            )
            ->setBindings([$range['from'], $range['to'], $range['from_old'], $range['to_old']])
            ->get()
            ->keyBy('name');

        return [
            'visitors' => $stats['visitors']->current_count ?? 0,
            'visitorsOld' => $stats['visitors']->old_count ?? 0,
            'pageviews' => $stats['pageviews']->current_count ?? 0,
            'pageviewsOld' => $stats['pageviews']->old_count ?? 0,
        ];
    }

    private function getWebsitesQuery(Request $request, $userId, $range)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['domain']) ? $request->input('search_by') : 'domain';
        $sortBy = in_array($request->input('sort_by'), ['id', 'domain']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';

        return Website::with([
            'visitors' => function ($query) use ($range) {
                $query->whereBetween('date', [$range['from'], $range['to']]);
            },
            'pageviews' => function ($query) use ($range) {
                $query->whereBetween('date', [$range['from'], $range['to']]);
            }
        ])
        ->where('user_id', $userId)
        ->when($search, function ($query) use ($search, $searchBy) {
            return $query->searchDomain($search);
        })
        ->orderBy($sortBy, $sort);
    }

    private function getPerPage(Request $request)
    {
        return in_array($request->input('per_page'), [10, 25, 50, 100])
            ? $request->input('per_page')
            : config('settings.paginate');
    }

    private function getQueryParams(Request $request, $range)
    {
        return [
            'from' => $range['from'],
            'to' => $range['to'],
            'search' => $request->input('search'),
            'search_by' => $request->input('search_by'),
            'sort_by' => $request->input('sort_by'),
            'sort' => $request->input('sort'),
            'per_page' => $request->input('per_page')
        ];
    }
}
