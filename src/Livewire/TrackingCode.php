<?php

namespace Shaferllc\Analytics\Livewire;

use App\Models\Site;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

#[Title('Tracking Code')]
#[Layout('analytics::layouts.app')]
class TrackingCode extends Component
{
    use DateRangeTrait, ComponentTrait;

    #[Locked]
    public Site $site;
    public function mount()
    {
        $this->range = $this->range();
    }

    public function render()
    {
        return view('analytics::livewire.tracking-code');
    }
}
