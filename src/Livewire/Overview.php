<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Illuminate\Support\Arr;
use Streamline\Models\Team;
use Illuminate\Http\Request;
use Livewire\Attributes\Layout;
use Illuminate\Contracts\View\View;
use Shaferllc\Analytics\Models\Stat;
use Illuminate\Support\Facades\Cache;
use App\Models\Website;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

/**
 * Class Overview
 * 
 * This component handles analytics data display and retrieval.
 */
class Overview extends Component
{
    use DateRangeTrait, ComponentTrait;

    public Team $currentTeam;
    public Website $website;

    public function mount(Website $website)
    {
        $this->website = $website;
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
        $now = now();

        $pages = Arr::get($this->query(
            type: 'page', 
            limit: 15,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'data');

        $totalPages = Arr::get($this->query(
            type: 'page', 
            limit: null,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'data')->count();

        $totalVisitorsOld = Arr::get($this->query(
            type: 'visitors', 
            limit: 5,
            from: $this->range['from_old'], 
            to: $this->range['to_old'],
            paginate: false
        ), 'total');

        $totalPageviewsOld = Arr::get($this->query(
            type: 'pageview', 
            limit: 5,
            from: $this->range['from_old'], 
            to: $this->range['to_old'],
            paginate: false
        ), 'total');

        $totalVisitors = Arr::get($this->query(
            type: 'visitors', 
            limit: null,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'data')->count();

        $totalPageviews = Arr::get($this->query(
            type: 'pageviews', 
            limit: null,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'data')->sum('count');

        $totalReferrers = Arr::get($this->query(
            type: 'referrer', 
            limit: 5,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'total');

        $countries = Arr::get($this->query(
            type: 'country', 
            limit: 5,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'data');

        $browsers = Arr::get($this->query(
            type: 'browser', 
            limit: 5,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'data');

        $operatingSystems = Arr::get($this->query(
            type: 'operating_system', 
            limit: 5,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'data');

        $events = Arr::get($this->query(
            type: 'event', 
            limit: 5,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'data');

        $referrers = Arr::get($this->query(
            type: 'referrer', 
            limit: 5,
            from: $this->range['from'], 
            to: $this->range['to'],
            paginate: false
        ), 'data');

        $pageviewsMap = $this->getStats($this->website, $this->range, 'pageviews');
        $visitorsMap = $this->getStats($this->website, $this->range, 'visitors');

        return view('analytics::livewire.overview', [
            'website' => $this->website,
            'range' => $this->range,
            'now' => $now,
            'pages' => $pages,
            'totalPages' => $totalPages,
            'pageviewsMap' => $pageviewsMap,
            'visitorsMap' => $visitorsMap,
            'totalReferrers' => $totalReferrers,
            'totalPageviewsOld' => $totalPageviewsOld,
            'totalVisitorsOld' => $totalVisitorsOld,
            'totalVisitors' => $totalVisitors,
            'totalPageviews' => $totalPageviews,
            'referrers' => $referrers,
            'countries' => $countries,
            'browsers' => $browsers,
            'operatingSystems' => $operatingSystems,
            'events' => $events
        ]);
    }
}
