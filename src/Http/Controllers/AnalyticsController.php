<?php

namespace ShaferLLC\Analytics\Http\Controllers;

use ShaferLLC\Analytics\Models\Stat;
use ShaferLLC\Analytics\Traits\DateRangeTrait;
use ShaferLLC\Analytics\Models\Website;
use Streamline\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalyticsController
{
    use DateRangeTrait;

    /**
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        
        $range = $this->range();
        $currentTeam = $request->user()->currentTeam();

        return $this->getStatsData($currentTeam, $range);
    }

    private function getSum(string $stat, string $rangeFrom, string $rangeTo)
    {
        $currentTeam = request()->user()->currentTeam();
        $cacheKey = "sum_{$stat}_{$currentTeam->id}_{$rangeFrom}_{$rangeTo}";

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($stat, $rangeFrom, $rangeTo, $currentTeam) {
            $sites = $currentTeam->websites()->pluck('id');

            return Stat::whereIn('website_id', $sites)
                ->where('name', $stat)
                ->whereBetween('date', [$rangeFrom, $rangeTo])
                ->sum('count');
        });
    }

    private function getStatsData(Team $currentTeam, array $range)
    {
        $now = now();

        $stats = [
            'visitors' => $this->getSum('visitors', $range['from'], $range['to']),
            'visitorsOld' => $this->getSum('visitors', $range['from_old'], $range['to_old']),
            'pageviews' => $this->getSum('pageviews', $range['from'], $range['to']),
            'pageviewsOld' => $this->getSum('pageviews', $range['from_old'], $range['to_old']),
        ];

        $search = request()->input('search');
        $searchBy = request()->input('search_by', 'domain');
        $favorite = request()->input('favorite');
        $sortBy = request()->input('sort_by', 'id');
        $sort = request()->input('sort', 'desc');
        $perPage = request()->input('per_page', config('settings.paginate'));

        $websitesQuery = Website::query()
            ->with(['visitors', 'pageviews'])
            ->where('team_id', $currentTeam->id)
            ->when($search, fn($query) => $query->searchDomain($search))
            ->when(isset($favorite) && is_numeric($favorite), fn($query) => $query->ofFavorite($favorite))
            ->orderBy($sortBy, $sort);

        $websites = $websitesQuery->paginate($perPage)->appends(request()->query());

        return view('analytics::index', [
            ...$stats,
            'range' => $range,
            'now' => $now,
            'websites' => $websites
        ]);
    }
}
