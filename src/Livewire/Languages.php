<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

class Languages extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Website $website;
    
    public function mount(Website $website)
    {
        $this->website = $website;   
    }

    #[Layout('analytics::layouts.app')]
    public function render()
    {
        $data = $this->query(
            type: 'language',
            to: $this->to,
            from: $this->from,
        );
      
        return view('analytics::livewire.languages', [
            'data' => $data['data'],
            'first' => $data['first'],
            'last' => $data['last'],
            'total' => $data['total'],
            'page' => 'languages',
            'range' => $this->range,
        ])->layoutData([
            'range' => $this->range,
            'page' => 'languages',
            'website' => $this->website,
        ]);
    }
}
