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

#[Title('Screen Resolutions')]
class ScreenResolutions extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    public $page = 0;

    public function render()
    {
        $perPage = 10000;

        $data = $this->getResolutions();

        $resolutions = $data['data'];

        $aggregates = $data['aggregates'];

        $resolutions = collect($resolutions)->sortByDesc('unique_visitors');

        $offset = max(0, ($this->page - 1) * $perPage);

        $items = $resolutions->slice($offset, $perPage + 1);

        return view('analytics::livewire.screen-resolutions', [
            'aggregates' => $aggregates,
            'resolutions' => new LengthAwarePaginator(
                $items,
                $resolutions->count(),
                $perPage,
                $this->page
            ),
        ]);
    }

    private function getResolutions()
    {
        return $this->visitorMetaData('resolution', [
            'viewport-width',
            'viewport-height',
            'device-type',
            'color-scheme',
            'platform',
            'vendor',
            'language',
        ]);
    }
}
