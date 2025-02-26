<?php

namespace Shaferllc\Analytics\Livewire;

use App\Models\Site;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Illuminate\Pagination\LengthAwarePaginator;

#[Title('SocialNetworks')]
class SocialNetworks extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;

    public function render()
    {
        $perPage = 10000;

        $socialNetworks = $this->getSocialNetworks();

        $aggregates = $socialNetworks['aggregates'];

        $socialNetworks = $socialNetworks['data'];

        $socialNetworks = collect($socialNetworks)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $socialNetworks->slice($offset, $perPage + 1);

        return view('analytics::livewire.social-networks', [
            'aggregates' => $aggregates,
            'socialNetworks' => new LengthAwarePaginator(
                $items,
                $socialNetworks->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function getSocialNetworks()
    {
        return $this->visitorMetaData('social-network', ['social-network-version']);
    }
}
