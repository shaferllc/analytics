<?php

namespace Shaferllc\Analytics\Livewire;

use App\Models\Site;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Illuminate\Support\Benchmark;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

#[Title('Sessions')]
class Sessions extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public function render()
    {

        [$visitors, $globalStats] = $this->sessions(
            to: $this->to,
            from: $this->from
        );


        return view('analytics::livewire.sessions', compact(
            'visitors',
            'globalStats'
        ));
    }
}

