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

#[Title('Operating systems')]
class OperatingSystems extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;


    public function render()
    {

        $perPage = 10000;

        $allData = $this->operatingSystems();

        $data = $allData['data'];

        $aggregates = $allData['aggregates'];

        $operatingSystems = collect($data)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $operatingSystems->slice($offset, $perPage + 1);

        return view('analytics::livewire.operating-systems', [
            'aggregates' => $aggregates,
            'operatingSystems' => new LengthAwarePaginator(
                $items,
                $operatingSystems->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function operatingSystems()
    {
        return $this->visitorMetaData('operating-system', ['vendor', 'platform', 'device-type', 'language']);
    }
}
