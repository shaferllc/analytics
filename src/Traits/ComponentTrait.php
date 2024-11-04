<?php

namespace Shaferllc\Analytics\Traits;

use Closure;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Shaferllc\Analytics\Models\Stat;
use Shaferllc\Analytics\Models\Recent;
use Shaferllc\Analytics\Models\Website;

trait ComponentTrait
{
    public $from = null;
    public $to = null;
    public $dateRange = 'today';
    public $range;
    public $search = '';
    public $sortBy = 'count';
    public $sort = 'desc';
    public $perPage = 10;
    private $query;
    public function boot(){
        $this->range = $this->range();
        $this->from = $this->range['from'];
        $this->to = $this->range['to'];
    }

    #[Computed]
    public function getFormattedDateRange(){
        return $this->range['from']->format('m/d/Y') . ' - ' . $this->range['to']->format('m/d/Y');
    }

    public function query(
        string $type,
        int $limit = null,
        Carbon $from = null,
        Carbon $to = null,
        bool $paginate = true,
        bool $unique = false,
        Closure $optionalQuery = null,
        $fromDateFormat = 'Y-m-d',
        string $toDateFormat = 'Y-m-d', 
        string $dateColumn = 'date'
    ){
        // Build base query with date range
        $query = $this->website->stats()
            ->where('name', $type)
            ->whereBetween($dateColumn, [$from->format($fromDateFormat), $to->format($toDateFormat)]);

        // Add select and group by based on unique flag
        if ($unique) {
            $query->select(['value'])
                ->selectRaw('COUNT(DISTINCT session_id) as count')
                ->selectRaw('COUNT(DISTINCT session_id) as total');
        } else {
            $query->select(['value'])
                ->selectRaw('SUM(`count`) as count')
                ->selectRaw('SUM(`count`) as total');
        }
        $query->groupBy('value');

        // Apply optional query modifications
        if ($optionalQuery) {
            $query = $optionalQuery($query);
        }

        // Get aggregate data in a separate query
        $aggregates = $this->website->stats()
            ->where('name', $type)
            ->whereBetween($dateColumn, [$from->format($fromDateFormat), $to->format($toDateFormat)])
            ->selectRaw('MAX(`count`) as max_count, MIN(`count`) as min_count, SUM(`count`) as total_count')
            ->first();

        // Apply search, sort and limit
        if($this->search) {
            $query->searchValue($this->search);
        }
        if($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sort);
        }
        if($limit) {
            $query->limit($limit);
        }

        // Get paginated/all results
        $data = $paginate ? $query->paginate($this->perPage) : $query->get();

        return [
            'data' => $data,
            'first' => (object)['count' => $aggregates->max_count ?? 0],
            'last' => (object)['count' => $aggregates->min_count ?? 0], 
            'total' => $aggregates->total_count ?? 0
        ];
    }
    public function recentQuery(
        int $limit = null, 
        Carbon $from = null, 
        Carbon $to = null, 
        bool $paginate = true, 
        bool $unique = false, 
        Closure $optionalQuery = null, 
        string $fromDateFormat = 'Y-m-d', 
        string $toDateFormat = 'Y-m-d', 
        string $dateColumn = 'date',
        bool $orderBy = true,
        string $orderByDirection = 'desc', 
        string $orderByColumn = 'id',
        array $select = ['*'],
        array $groupBy = [],
        bool $withCurrentVisitors = false
    ){
        // Start with base query and eager load relationships if needed
        $query = $this->website->recents();

        // Apply date range filter using index
        $query->whereBetween($dateColumn, [
            $from->format($fromDateFormat), 
            $to->format($toDateFormat)
        ])->when($select !== ['*'], function($q) use ($select) {
            // Only select specific columns if provided
            return $q->select($select);
        });

        // Apply optional query modifications
        if ($optionalQuery) {
            $query = $optionalQuery($query);
        }

        // Handle unique counts if needed
        if ($unique) {
            $query->select('value')
                  ->selectRaw('COUNT(DISTINCT session_id) as count')
                  ->groupBy('value');
        }

        // Apply grouping and optimize order
        if (!empty($groupBy)) {
            $query->groupBy($groupBy);
            
            // Optimize order by using grouped column
            if ($orderBy && !in_array($orderByColumn, $groupBy)) {
                $orderByColumn = $groupBy[0];
            }
        }

        

        // Apply limit before pagination/fetching
        if ($limit) {
            $query->limit($limit);
        }

        // Apply ordering
        if ($orderBy) {
            $query->orderBy($orderByColumn, $orderByDirection);
        }

        // Use chunk() for large datasets if needed
        $data = $paginate ? 
            $query->paginate($this->perPage) : 
            $query->get();

        return ['data' => $data];
    }
    public function updateDateRange($range){

        $this->dateRange = $range;
        $this->from = null;
        $this->to = null;
        switch ($range) {
            case 'today':
                $this->from = Carbon::today();
                $this->to = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $this->from = Carbon::yesterday();
                $this->to = Carbon::yesterday()->endOfDay();
                break;
            case 'last7days':
                $this->from = Carbon::now()->subDays(6)->startOfDay();
                $this->to = Carbon::now()->endOfDay();
                break;
            case 'last30days':
                $this->from = Carbon::now()->subDays(29)->startOfDay();
                $this->to = Carbon::now()->endOfDay();
                break;
            case 'thisMonth':
                $this->from = Carbon::now()->startOfMonth();
                $this->to = Carbon::now()->endOfMonth();
                break;
            case 'lastMonth':
                $this->from = Carbon::now()->subMonth()->startOfMonth();
                $this->to = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'last3Months':
                $this->from = Carbon::now()->subMonths(2)->startOfMonth();
                $this->to = Carbon::now()->endOfMonth();
                break;
            case 'last6Months':
                $this->from = Carbon::now()->subMonths(5)->startOfMonth();
                $this->to = Carbon::now()->endOfMonth();
                break;
            case 'thisYear':
                $this->from = Carbon::now()->startOfYear();
                $this->to = Carbon::now()->endOfYear();
                break;
            case 'lastYear':
                $this->from = Carbon::now()->subYear()->startOfYear();
                $this->to = Carbon::now()->subYear()->endOfYear();
                break;
            case 'all':
                $this->from = Carbon::now()->subYears(100);
                $this->to = Carbon::now();
                break;
            case 'custom':
                // Do nothing, as custom range is handled separately
                break;
        }
        $from = Carbon::parse($this->from)->startOfDay();
        $to = Carbon::parse($this->to)->endOfDay();
        $this->range = $this->range($from, $to);
    }

    public function resetFilters(){
        $this->reset(['search', 'sortBy', 'sort', 'perPage']);
    }

    public function setSortBy($sortBy){
        $this->sortBy = $sortBy;
        $this->sort = $this->sort == 'desc' ? 'asc' : 'desc';
    }

    private function getStats(Website $website, array $range, string$type)
    {
        $output = [];

        if ($range['unit'] == 'hour') {
            $output = array_fill_keys(array_map(function($i) { return sprintf('%02d', $i); }, range(0, 23)), 0);

            $rows = Stat::where([
                    ['website_id', '=', $website->id],
                    ['name', '=', $type . '_hour']
                ])
                ->whereBetween('date', [$range['from'], $range['to']])
                ->orderBy('date', 'asc')
                ->pluck('count', 'value');

            foreach ($rows as $hour => $count) {
                $output[$hour] = $count;
            }
        } else {
            $rangeMap = $this->calcAllDates(
                Carbon::parse($range['from'])->format($range['format']),
                Carbon::parse($range['to'])->format($range['format']),
                $range['unit'],
                $range['format'],
                0
            );

            $rows = Stat::select([
                    DB::raw("date_format(`date`, '". str_replace(['Y', 'm', 'd'], ['%Y', '%m', '%d'], $range['format'])."') as `date_result`"),
                    DB::raw("SUM(`count`) as `aggregate`")
                ])
                ->where([
                    ['website_id', '=', $website->id],
                    ['name', '=', $type]
                ])
                ->whereBetween('date', [$range['from'], $range['to']])
                ->groupBy('date_result')
                ->orderBy('date_result', 'asc')
                ->pluck('aggregate', 'date_result')
                ->toArray();

            $output = array_replace($rangeMap, $rows);
        }

        return $output;
    }
}