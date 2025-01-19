<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use App\Models\Site;
use Illuminate\Support\Arr;
use Streamline\Models\Team;
use Livewire\Attributes\Title;
use Illuminate\Contracts\View\View;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

#[Title('Realtime Analytics')]
class Realtime extends Component
{
    use DateRangeTrait, ComponentTrait;

    public Team $currentTeam;
    public Site $site;

    public function mount(Site $site)
    {
        $this->site = $site;
        $this->currentTeam = request()->user()->currentTeam();

        // Set range to last 5 minutes
        $this->range = [
            'from' => now()->subMinutes(5),
            'to' => now()
        ];
    }

    public function render(): View
    {

        $this->dispatch('refreshChart');
        $data = [
            'activeVisitors' => $this->activeVisitors(
                $this->range['from'],
                $this->range['to']
            ),
            'pageviews' => $this->pageviews(
                $this->range['from'],
                $this->range['to']
            ),
            'pages' => $this->currentPages(
                $this->range['from'],
                $this->range['to']
            ),
            'timezones' => $this->timezones(
                $this->range['from'],
                $this->range['to']
            ),
            // 'countries' => $this->getCountries(),
            // 'referrers' => $this->getReferrers()
        ];


        dd($data);

        return view('analytics::livewire.realtime', [
            'activeVisitors' => $data['activeVisitors'],
            'pageviews' => $data['pageviews'],
            'pages' => $data['pages'],
            'countries' => $data['countries'],
            'referrers' => $data['referrers']
        ]);
    }


    protected function getCountries()
    {
        return Arr::get($this->query(
            type: 'country',
            limit: 10,
            from: $this->range['from'],
            to: $this->range['to'],
            paginate: false
        ), 'data');
    }

    protected function getReferrers()
    {
        return Arr::get($this->query(
            type: 'referrer',
            limit: 10,
            from: $this->range['from'],
            to: $this->range['to'],
            paginate: false
        ), 'data');
    }
}
