<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Streamline\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Shaferllc\Analytics\Models\Stat;
use Illuminate\Support\Facades\Cache;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

/**
 * Class AnalyticsController
 * 
 * This controller handles analytics-related operations and data retrieval.
 */
class Analytics extends Component
{
    use DateRangeTrait, ComponentTrait;

    public $currentTeam;
    /**
     * Display the analytics index page with statistics data.
     *
     * @param Request $request The incoming HTTP request.
     * @return \Illuminate\Contracts\View\View
     */
    public function mount()
    {
        $this->range = $this->range();
        $this->currentTeam = auth()->user()->currentTeam();
    }

    /**
     * Get the sum of a specific statistic for a given date range.
     *
     * @param string $stat The name of the statistic to sum.
     * @param string $rangeFrom The start date of the range.
     * @param string $rangeTo The end date of the range.
     * @return int The sum of the statistic.
     */
    private function getSum(string $stat, string $rangeFrom, string $rangeTo): int
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

    /**
     * Retrieve and compile statistics data for the given team and date range.

     */
    public function render()
    {
        $now = now();

        $stats = [
            'visitors' => $this->getSum('visitors', $this->range['from'], $this->range['to']),
            'visitorsOld' => $this->getSum('visitors', $this->range['from_old'], $this->range['to_old']),
            'pageviews' => $this->getSum('pageviews', $this->range['from'], $this->range['to']),
            'pageviewsOld' => $this->getSum('pageviews', $this->range['from_old'], $this->range['to_old']),
        ];

        $search = request()->input('search');
        $searchBy = request()->input('search_by', 'domain');
        $favorite = request()->input('favorite');
        $sortBy = request()->input('sort_by', 'id');
        $sort = request()->input('sort', 'desc');
        $perPage = request()->input('per_page', 25);

            $websitesQuery = Website::query()
            ->with(['visitors', 'pageviews'])
            ->where('team_id', $this->currentTeam->id)
            ->when($search, fn($query) => $query->searchDomain($search))
            ->when(isset($favorite) && is_numeric($favorite), fn($query) => $query->ofFavorite($favorite))
            ->orderBy($sortBy, $sort);

        $websites = $websitesQuery->paginate($perPage)->appends(request()->query());

        return view('analytics::livewire.analytics', [
            ...$stats,
            'range' => $this->range,
            'now' => $now,
            'websites' => $websites
        ]);
    }
}
