<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Shaferllc\Analytics\Models\Recent;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Asantibanez\LivewireCharts\Facades\LivewireCharts;
use Livewire\Features\SupportPagination\HandlesPagination;

class Realtime extends Component
{
    use DateRangeTrait, ComponentTrait, HandlesPagination;
    
    public Website $website;
    public array $visitorsCount = [];
    public array $pageviewsCount = [];
    public int $subMinutes = 5; // Look at last 5 minutes
    public int $interval = 5;
    public array $visitorsMap;
    public array $pageviewsMap; 
    public int $totalVisitors = 0;
    public int $totalPageviews = 0;

    public bool $isPaused = false;
    protected $lastPausedData = null;
    public bool $groupSimilarPages = false;

    public array $visitorCounts = [];
    public array $pageviewCounts = [];

    public $realtimeTo = null;
    public $realtimeFrom = null;

    public function mount(Website $website)
    {
        $this->website = $website;
    }

    public function booted(): void
    {
        // Set time range to last 5 minutes
        $now = Carbon::now();
        $this->realtimeTo = $now;
        $this->realtimeFrom = $this->realtimeTo->copy()->subMinutes($this->subMinutes);

        $this->visitorCounts();
        $this->pageviewCounts();
    }

    public function togglePause()
    {
        $this->isPaused = !$this->isPaused;
        if ($this->isPaused) {
            // Store current data when pausing
            $this->lastPausedData = $this->recent();
        } else {
            // Clear stored data when unpausing
            $this->lastPausedData = null;
        }
    }

    public function visitorCounts()
    {
        // Get pageview counts from database
        // Get all minutes between from and to
        $minutes = [];
        $current = $this->realtimeFrom->copy();
        while ($current <= $this->realtimeTo) {
            $minutes[$current->format('Y-m-d H:i:00')] = 0;
            $current->addMinute();
        }

        // Get actual pageview counts - count all page hits
        $counts = $this->website->recents()
            ->selectRaw('COUNT(DISTINCT session_id) as count')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:00") as minute')
            ->where(function ($query) {
                $query->where('referrer', '<>', $this->website->domain)
                    ->orWhereNull('referrer');
                })  
            ->whereBetween('created_at', [
                $this->realtimeFrom->format('Y-m-d H:i:s'),
                $this->realtimeTo->format('Y-m-d H:i:s')
            ])
            ->groupBy('minute')
            ->orderBy('minute')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->minute => $item->count];
            })
            ->toArray();

        // Merge actual counts with empty minutes
        $this->pageviewCounts = array_merge($minutes, $counts);
        ksort($this->pageviewCounts);

        $this->dispatch('pageviewCountsUpdated', $this->pageviewCounts);
    }

    public function pageviewCounts()
    {
        // Get visitor counts from database
        // Get all minutes between from and to
        $minutes = [];
        $current = $this->realtimeFrom->copy();
        while ($current <= $this->realtimeTo) {
            $minutes[$current->format('Y-m-d H:i:00')] = 0;
            $current->addMinute();
        }

        $counts = $this->website->recents()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:%i:00") as minute')
            ->selectRaw('COUNT(*) as count')
            ->whereBetween('created_at', [
                $this->realtimeFrom->format('Y-m-d H:i:s'),
                $this->realtimeTo->format('Y-m-d H:i:s')
            ])
            ->groupBy('minute')
            ->orderBy('minute')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->minute => $item->count];
            })
            ->toArray();
        // Merge actual counts with empty minutes
        $this->visitorCounts = array_merge($minutes, $counts);
        ksort($this->visitorCounts);

        $this->dispatch('visitorCountsUpdated', $this->visitorCounts);
    }  

    #[Computed]
    public function recent()
    {
        // Return stored data if paused
        if ($this->isPaused && $this->lastPausedData !== null) {
            return $this->lastPausedData;
        }

        // Get recent pageviews with current visitor count for each page
        $query = $this->recentQuery(
            from: $this->realtimeFrom,
            to: $this->realtimeTo,
            paginate: true,
            fromDateFormat: 'Y-m-d H:i:s',
            toDateFormat: 'Y-m-d H:i:s',
            dateColumn: 'created_at',
            orderBy: true,
            orderByDirection: 'desc',
            orderByColumn: 'id',
            withCurrentVisitors: true // Add current visitor count for each page
        );

        // Get sum of current visitors for each page
        $currentVisitors = $this->website->recents()
            ->selectRaw('page, COUNT(DISTINCT session_id) as current_visitors')
            ->whereBetween('created_at', [
                $this->realtimeFrom->format('Y-m-d H:i:s'),
                $this->realtimeTo->format('Y-m-d H:i:s')
            ])
            ->groupBy('page')
            ->get()
            ->pluck('current_visitors', 'page')
            ->toArray();

        // Add current visitor count to each page in results
        $query['data']->through(function($item) use ($currentVisitors) {
            $item->current_visitors = $currentVisitors[$item->page] ?? 0;
            return $item;
        });
        $data = Arr::get($query, 'data');

       

        if ($this->groupSimilarPages) {
            return $this->groupSimilarPageviews($data);
        }

        return $data;
    }

    protected function groupSimilarPageviews($pageviews)
    {
        // Convert LengthAwarePaginator to Collection
        $collection = collect($pageviews->items());
        
        return $collection->groupBy('page')
            ->map(function ($group) {
                $first = $group->first();
                $first->current_visitors = $group->sum('current_visitors');
                return $first;
            })
            ->values();
    }

    #[Layout('analytics::layouts.app')]
    public function render()
    {

        return view('analytics::livewire.realtime', [
            'website' => $this->website,
            'range' => $this->range,
            'page' => 'realtime'
        ])->layoutData([
            'range' => $this->range,
            'page' => 'realtime',
            'website' => $this->website,
        ]);
    }
}
