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

#[Title('Pages')]
class Pages extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;

    public function render()
    {
        $perPage = 10000;

        $allData = $this->pages();

        $data = $allData['data'];

        $aggregates = $allData['aggregates'];

        $pages = collect($data)->sortByDesc('unique_visitors');

        dd($pages);
        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $pages->slice($offset, $perPage + 1);


        return view('analytics::livewire.pages', [
            'aggregates' => $aggregates,
            'pages' => new LengthAwarePaginator(
                $items,
                $pages->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function pages()
    {
        return $this->visitorMetaData('page', ['page-version']);
    }
}
