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

#[Title('Timezones')]
class Timezones extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;

    public function render()
    {
        $perPage = 10000;

        $allData = $this->timezones();

        $data = $allData['data'];

        $aggregates = $allData['aggregates'];

        $timezones = collect($data)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $timezones->slice($offset, $perPage + 1);

        $timezoneMap = collect($data)->map(function ($item) {
            return [
                'name' => $item['value'],
                'value' => $item['unique_visitors']
            ];
        });

        return view('analytics::livewire.timezones', [
            'aggregates' => $aggregates,
            'timezoneMap' => $timezoneMap,
            'timezones' => new LengthAwarePaginator(
                $items,
                $timezones->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function timezones()
    {
        return $this->visitorMetaData('timezone');
    }
}
