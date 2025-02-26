<?php

namespace Shaferllc\Analytics\Traits;

use Illuminate\Support\Arr;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;

trait SimpleAnalyticsPagination
{
    public const PER_PAGE = 10;

    #[Computed]
    public function pageViews(){
        $pages = $this->site->pages()
            ->select('path', 'page_view_count', 'visit_count', 'title')
            ->whereBetween('created_at', [$this->range['from'], $this->range['to']])
            ->orderBy('page_view_count', 'desc')
            ->simplePaginate(self::PER_PAGE, pageName: 'page-views')
            ->through(fn($item) => [
                'name' => $item->path,
                'total' => $item->page_view_count,

                // Extra data
                'url' => $item->path,
                'title' => $item->title,
                'visit_count' => $item->visit_count,
            ]);

        // Optimize by getting totals in a single query
        $totals = $this->site->pages()
            ->whereBetween('created_at', [$this->range['from'], $this->range['to']])
            ->selectRaw('SUM(page_view_count) as total_page_views_count, SUM(visit_count) as total_visitors_count')
            ->first();

        $pages->total_page_views_count = $totals->total_page_views_count ?? 0;
        $pages->total_visitors_count = $totals->total_visitors_count ?? 0;

        return $pages;
    }

    #[Computed]
    public function countries(){
        return $this->site->visitors()
            ->getQuery()
            ->select('country', DB::raw('COUNT(*) as count'))
            ->whereBetween('analytics_visitors.created_at', [$this->range['from'], $this->range['to']])
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->simplePaginate(self::PER_PAGE, pageName: 'countries')
        ->through(fn($item) => [
            'name' => $item->country ?? 'Unknown',
            'total' => $item->count
        ]);
    }

    #[Computed]
    public function browsers(){
        return $this->getVistorMetaData(
            between: 'analytics_visitors.created_at',
            event_name: 'browser_data',
            meta_type: 'browser',
            meta_key: 'browser'
        );
    }
}
