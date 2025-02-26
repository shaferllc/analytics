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

#[Title('Languages')]
class Languages extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;
    public function render()
    {

        $perPage = 10000;

        $allData = $this->languages();

        $data = $allData['data'];

        $aggregates = $allData['aggregates'];

        $languages = collect($data)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $languages->slice($offset, $perPage + 1);
;

        return view('analytics::livewire.languages', [
            'aggregates' => $aggregates,
            'languages' => new LengthAwarePaginator(
                $items,
                $languages->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function languages()
    {
        return $this->visitorMetaData('language');
    }
}
