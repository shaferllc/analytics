<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use Streamline\Models\Team;
use Illuminate\Http\Request;
use Livewire\Attributes\Title;
use Illuminate\Contracts\View\View;
use Shaferllc\Analytics\Models\Stat;
use Illuminate\Support\Facades\Cache;
use App\Models\Site;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

#[Title('Analytics Overview')]
class Overview extends Component
{
    use DateRangeTrait, ComponentTrait;

    public Team $currentTeam;
    public Site $site;

    public function mount()
    {
        $this->range = $this->range();
        $this->currentTeam = request()->user()->currentTeam();
    }

    /**
     * Retrieve and compile statistics data for display.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): View
    {


        dd($this->pages());
        $data = [
            'pages' => $this->getPages(),
            'visitors' => $this->getVisitors(),
            'pageviews' => $this->getPageviews(),
            'referrers' => $this->getReferrers(),
            'countries' => $this->getCountries(),
            'browsers' => $this->getBrowsers(),
            'operatingSystems' => $this->getOperatingSystems(),
            'events' => $this->getEvents(),
            'stats' => $this->getWebsiteStats(),
        ];

        return view('analytics::livewire.overview', [
            'range' => $this->range,
            'now' => now(),
            'pages' => $data['pages'],
            'totalPages' => $data['pages']->count(),
            'pageviewsMap' => $data['stats']['pageviews'],
            'visitorsMap' => $data['stats']['visitors'],
            'totalReferrers' => $data['referrers']['total'],
            'totalPageviewsOld' => $data['pageviews']['old'],
            'totalVisitorsOld' => $data['visitors']['old'],
            'totalVisitors' => $data['visitors']['current'],
            'totalPageviews' => $data['pageviews']['current'],
            'referrers' => $data['referrers']['data'],
            'countries' => $data['countries'],
            'browsers' => $data['browsers'],
            'operatingSystems' => $data['operatingSystems'],
            'events' => $data['events'],
            'avgSessionDuration' => $data['stats']['avgSessionDuration']
        ]);
    }

    protected function getVisitors()
    {
        return [
            'old' => Arr::get($this->query(
                type: 'session_id',
                limit: 5,
                from: $this->range['from_old'],
                to: $this->range['to_old'],
                paginate: false
            ), 'total'),
            'current' => Arr::get($this->query(
                type: 'session_id',
                limit: null,
                from: $this->range['from'],
                to: $this->range['to'],
                paginate: false
            ), 'data')->count()
        ];
    }

    protected function getPageviews()
    {
        return [
            'old' => Arr::get($this->query(
                type: 'page',
                limit: 5,
                from: $this->range['from_old'],
                to: $this->range['to_old'],
                paginate: false
            ), 'total'),
            'current' => Arr::get($this->query(
                type: 'page',
                limit: null,
                from: $this->range['from'],
                to: $this->range['to'],
                paginate: false
            ), 'data')->sum('count')
        ];
    }

    protected function getReferrers()
    {
        return [
            'total' => Arr::get($this->query(
                type: 'referrer',
                limit: 5,
                from: $this->range['from'],
                to: $this->range['to'],
                paginate: false
            ), 'total'),
            'data' => Arr::get($this->query(
                type: 'referrer',
                limit: 5,
                from: $this->range['from'],
                to: $this->range['to'],
                paginate: false
            ), 'data')
        ];
    }

    protected function getCountries()
    {
        return Arr::get($this->query(
            type: 'country',
            limit: 5,
            from: $this->range['from'],
            to: $this->range['to'],
            paginate: false
        ), 'data');
    }

    protected function getBrowsers()
    {
        return Arr::get($this->query(
            type: 'browser',
            limit: 5,
            from: $this->range['from'],
            to: $this->range['to'],
            paginate: false
        ), 'data');
    }

    protected function getOperatingSystems()
    {

        return Arr::get($this->query(
            type: 'os',
            limit: 5,
            from: $this->range['from'],
            to: $this->range['to'],
            paginate: false
        ), 'data');
    }

    protected function getEvents()
    {
        return Arr::get($this->query(
            type: 'events',
            limit: 5,
            from: $this->range['from'],
            to: $this->range['to'],
            paginate: false
        ), 'data');
    }

    protected function getWebsiteStats()
    {
        return [
            'pageviews' => $this->getStats($this->site, $this->range, 'page'),
            'visitors' => $this->getStats($this->site, $this->range, 'session_id'),
            'avgSessionDuration' => $this->site->stats()
                ->whereBetween('created_at', [$this->range['from'], $this->range['to']])
                ->where('name', 'time_on_page')
                ->avg('value') ?? 0
        ];
    }
}
