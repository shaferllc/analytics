<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use App\Models\Site;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

#[Title('Referrers')]
class Referrers extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;



    public function render()
    {

        $data = $this->query(
            category: 'source',
            type:'referrer',
            to: $this->to,
            from: $this->from,
        );

    return view('analytics::livewire.referrers', [
            'data' => $data['data'],
            'first' => $data['first'],
            'last' => $data['last'],
            'total' => $data['total'],
            'aggregates' => $data['aggregates'],
            'range' => $this->range,
        ]);
    }
}
