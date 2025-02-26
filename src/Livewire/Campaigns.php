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

#[Title('Campaigns')]
class Campaigns extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;

    public function render()
    {
        $perPage = 10000;

        $campaigns = $this->getCampaigns();

        $aggregates = $campaigns['aggregates'];

        $campaigns = $campaigns['data'];

        $campaigns = collect($campaigns)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $campaigns->slice($offset, $perPage + 1);

        return view('analytics::livewire.campaigns', [
            'aggregates' => $aggregates,
            'campaigns' => new LengthAwarePaginator(
                $items,
                $campaigns->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function getCampaigns()
    {
        return $this->visitorMetaData('campaign', ['campaign-version']);
    }
}
