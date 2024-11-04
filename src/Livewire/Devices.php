<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

class Devices extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Website $website;

    public $dateRange = 'today';
    public function mount(Website $website)
    {
        $this->website = $website;   
    }


    #[Layout('analytics::layouts.app')]
    public function render()
    {
        $data = $this->query(
            type:'device',
            from: $this->from,
            to: $this->to
        );
      
        return view('analytics::livewire.devices', [
            'data' => $data['data'],
            'first' => $data['first'],
            'last' => $data['last'],
            'total' => $data['total'],
            'page' => 'devices',
            'range' => $this->range,
        ])->layoutData([
            'range' => $this->range,
            'page' => 'devices',
            'website' => $this->website,
        ]);
    }
}
