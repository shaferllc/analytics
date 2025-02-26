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

#[Title('Browsers')]
class Browsers extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;

    public function render()
    {
        $perPage = 10000;

        $browsers = $this->getBrowsers();

        $aggregates = $browsers['aggregates'];
        $browsers = $browsers['data'];

        $browsers = collect($browsers)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $browsers->slice($offset, $perPage + 1);

        return view('analytics::livewire.browsers', [
            'aggregates' => $aggregates,
            'browsers' => new LengthAwarePaginator(
                $items,
                $browsers->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function getBrowsers()
    {
        return $this->visitorMetaData('browser-name', ['browser-version', 'border-language', 'color-scheme', 'platform', 'vendor', 'language']);
    }
}
