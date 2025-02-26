<?php

namespace Shaferllc\Analytics\Livewire;

use App\Models\Site;
use Livewire\Component;
use Streamline\Models\Team;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Illuminate\Contracts\View\View;
use Shaferllc\Analytics\Traits\AnalyticsTrait;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Shaferllc\Analytics\Traits\SimpleAnalyticsPagination;

#[Title('Analytics Overview')]
class Overview extends Component
{
    use DateRangeTrait, ComponentTrait, WithPagination, SimpleAnalyticsPagination;

    #[Locked]
    public Team $currentTeam;

    #[Locked]
    public Site $site;

    public function render(): View
    {
        return view('analytics::livewire.overview');
    }

    #[Computed]
    public function pages()
    {
        return $this->getPages();
    }

    #[Computed]
    public function trafficSources()
    {
        return $this->getVistorMetaData(
            between: 'analytics_visitors.created_at',
            event_name: 'page_data',
            meta_type: 'referrer',
            meta_key: 'referrer'
        );
    }

    #[Computed]
    public function chartColors(): array
    {
        return [
            'primary' => '#007bff',
            'secondary' => '#6c757d',
        ];
    }

    #[Computed]
    public function totalVisitors(): array
    {
        return [
            'current' => $this->site->visitors()
                ->whereBetween('analytics_visitors.created_at', [$this->range['from'], $this->range['to']])
                ->count(),
            'old' => $this->site->visitors()
                ->whereBetween('analytics_visitors.created_at', [$this->range['from_old'], $this->range['to_old']])
                ->count(),
        ];
    }

    #[Computed]
    public function totalPageviews(): array
    {
        return [
            'current' => $this->site->pages()
                ->whereBetween('created_at', [$this->range['from'], $this->range['to']])
                ->count(),
            'old' => $this->site->pages()
                ->whereBetween('created_at', [$this->range['from_old'], $this->range['to_old']])
                ->count(),
        ];
    }

    #[Computed]
    public function avgSessionDuration(): float
    {
        return $this->site->sessions()
            ->whereBetween('start_session_at', [$this->range['from'], $this->range['to']])
            ->avg('total_duration_seconds') ?? 0;
    }

    #[Computed]
    public function visitorsMap(): array
    {
        return $this->site->visitors()
            ->whereBetween('analytics_visitors.created_at', [$this->range['from'], $this->range['to']])
            ->get()
            ->groupBy(fn($visitor) => $visitor->meta_data->get('country') ?? 'Unknown')
            ->mapWithKeys(fn($items, $country) => [$country => $items->count()])
            ->toArray();
    }


    #[Computed]
    public function pagesPerSession(): float
    {
        return $this->site->sessions()
            ->whereBetween('start_session_at', [$this->range['from'], $this->range['to']])
            ->groupBy('visitor_id')
            ->count('page_id') / max(1, $this->site->sessions()->count()) ?? 0;
    }


    #[Computed]
    public function pageviewsMap(): array
    {
        return $this->site->pages()
            ->selectRaw('path, count(*) as total')
            ->whereBetween('created_at', [$this->range['from'], $this->range['to']])
            ->groupBy('path')
            ->get()
            ->mapWithKeys(fn($item) => [$item->path => $item->total])
            ->toArray();
    }



    #[Computed]
    public function browsersMap(): LengthAwarePaginator
    {
        return $this->getVistorMetaData(
            between: 'analytics_visitors.created_at',
            event_name: 'browser_data',
            meta_type: 'browser',
            meta_key: 'browser'
        );
    }

    #[Computed]
    public function devicesMap(): LengthAwarePaginator
    {
        return $this->getVistorMetaData(
            between: 'analytics_visitors.created_at',
            event_name: 'browser_data',
            meta_type: 'device_type',
            meta_key: 'device_type'
        );
    }

    #[Computed]
    public function referrersMap(): LengthAwarePaginator
    {
        return $this->getVistorMetaData(
            between: 'analytics_visitors.created_at',
            event_name: 'page_data',
            meta_type: 'referrer',
            meta_key: 'referrer'
        );
    }

    #[Computed]
    public function operatingSystemsMap(): LengthAwarePaginator
    {
        return $this->getVistorMetaData(
            between: 'analytics_visitors.created_at',
            event_name: 'browser_data',
            meta_type: 'user_agent',
            meta_key: 'platform'
        );
    }

    #[Computed]
    public function bounceRate(): float
    {
        $totalSessions = $this->site->sessions()
            ->whereBetween('start_session_at', [$this->range['from'], $this->range['to']])
            ->count();

        $singlePageSessions = $this->site->sessions()
            ->whereBetween('start_session_at', [$this->range['from'], $this->range['to']])
            ->select('visitor_id')
            ->groupBy('visitor_id')
            ->havingRaw('COUNT(DISTINCT page_id) = 1')
            ->get()
            ->count();

        return $totalSessions > 0 ? ($singlePageSessions / $totalSessions) * 100 : 0;
    }

    #[Computed]
    public function newVsReturningVisitors(): array
    {
        $visitors = $this->site->visitors()
            ->whereBetween('analytics_visitors.created_at', [$this->range['from'], $this->range['to']])
            ->get();

        $counts = $visitors->countBy(fn($visitor) => $visitor->total_visits === 1 ? 'new' : 'returning');
        return [
            'new' => $counts->get('new', 0),
            'returning' => $counts->get('returning', 0),
        ];
    }
}
