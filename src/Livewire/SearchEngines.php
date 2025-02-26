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

#[Title('SearchEngines')]
class SearchEngines extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;

    public function render()
    {
        $perPage = 10000;

        $searchEngines = $this->getSearchEngines();

        dd($searchEngines);
        $aggregates = $searchEngines['aggregates'];

        $searchEngines = $searchEngines['data'];

        $searchEngines = collect($searchEngines)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $searchEngines->slice($offset, $perPage + 1);

        return view('analytics::livewire.search-engines', [
            'aggregates' => $aggregates,
            'searchEngines' => new LengthAwarePaginator(
                $items,
                $searchEngines->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function getSearchEngines()
    {
        return $this->visitorMetaData('search-engine', ['search-engine-version']);
    }
}
