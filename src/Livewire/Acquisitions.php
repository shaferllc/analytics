<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use App\Models\Site;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Shaferllc\Analytics\Traits\ComponentTrait;

#[Title('Acquisitions')]
class Acquisitions extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;


    public function render()
    {
        $referrers = $this->query(
            type: 'referrer',
            from: $this->from,
            to: $this->to
        );

        $searchEngines = $this->query(
            type: 'search_engine',
            from: $this->from,
            to: $this->to
        );

        $socialNetworks = $this->query(
            type: 'social_network',
            from: $this->from,
            to: $this->to
        );

        $campaigns = $this->query(
            type: 'campaign',
            from: $this->from,
            to: $this->to
        );


        return view('analytics::livewire.acquisitions', [
            'referrers' => $referrers['total'] ?? 0,
            'searchEngines' => $searchEngines['total'] ?? 0,
            'socialNetworks' => $socialNetworks['total'] ?? 0,
            'campaigns' => $campaigns['total'] ?? 0,
        ]);
    }

}
