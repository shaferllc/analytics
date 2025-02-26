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

#[Title('Cities')]
class Cities extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;
    public function render()
    {

        $perPage = 10000;

        $allData = $this->cities();

        $data = $allData['data'];

        $aggregates = $allData['aggregates'];

        $cities = collect($data)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $cities->slice($offset, $perPage + 1);

        $cityMap = collect($data)->map(function ($item) {
            return [
                'name' => $item['value'],
                'value' => $item['unique_visitors']
            ];
        });

        return view('analytics::livewire.cities', [
            'aggregates' => $aggregates,
            'cityMap' => $cityMap,
            'cities' => new LengthAwarePaginator(
                $items,
                $cities->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function cities()
    {
        return $this->visitorMetaData('city');
    }
}
