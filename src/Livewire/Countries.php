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

#[Title('Countries')]
class Countries extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;
    public function render()
    {

        $perPage = 10000;

        $allData = $this->countries();

        $data = $allData['data'];

        $aggregates = $allData['aggregates'];

        $countries = collect($data)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $countries->slice($offset, $perPage + 1);

        $countryMap = collect($data)->map(function ($item) {
            return [
                'name' => $item['value'],
                'value' => $item['unique_visitors']
            ];
        });

        return view('analytics::livewire.countries', [
            'aggregates' => $aggregates,
            'countryMap' => $countryMap,
            'countries' => new LengthAwarePaginator(
                $items,
                $countries->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function countries()
    {
        return $this->visitorMetaData('country');
    }
}
