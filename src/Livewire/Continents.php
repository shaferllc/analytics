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

#[Title('Continents')]
class Continents extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;
    public function render()
    {

        $perPage = 10000;

        $allData = $this->continents();

        $data = $allData['data'];

        $aggregates = $allData['aggregates'];

        $continents = collect($data)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $continents->slice($offset, $perPage + 1);

        $continentMap = collect($data)->map(function ($item) {
            return [
                'name' => $item['value'],
                'value' => $item['unique_visitors']
            ];
        });

        return view('analytics::livewire.continents', [
            'aggregates' => $aggregates,
            'continentMap' => $continentMap,
            'continents' => new LengthAwarePaginator(
                $items,
                $continents->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function continents()
    {
        return $this->visitorMetaData('continent');
    }
}
