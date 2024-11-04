<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

class Pages extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Website $website;
    public function mount(Website $website)
    {
        $this->range = $this->range();
        $this->website = $website;   
    }

    #[Layout('analytics::layouts.app')]
    public function render()
    {
        $data = $this->query(
            type: 'page',
            limit: null,
            from: $this->range['from'],
            to: $this->range['to'],
            paginate: true,
        );

        return view('analytics::livewire.pages', [
            'data' => $data['data'],
            'first' => $data['first'],
            'last' => $data['last'],
            'total' => $data['total'],
            'page' => 'pages',
            'range' => $this->range,
        ])->layoutData([
            'range' => $this->range,
            'page' => 'pages',
            'website' => $this->website,
        ]);
    }
}
