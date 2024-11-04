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
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

/**
 * Class AnalyticsController
 * 
 * This controller handles analytics-related operations and data retrieval.
 */
class TrackingCode extends Component
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
     * Retrieve and compile statistics data for the given team and date range.

     */
    #[Layout('analytics::layouts.app')]
    public function render()
    {
     
    
        return view('analytics::livewire.tracking-code', [
            'website' => $this->website,
            'range' => $this->range,
        ]);
    }
}
