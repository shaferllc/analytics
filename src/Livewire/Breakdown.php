<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use App\Models\Site;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

#[Title('Breakdown')]
class Breakdown extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;

    #[Locked]
    public string $name;

    #[Locked]
    public string $value;

    public function mount(Site $site, $name, $value): void
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function render()
    {
        $data = $this->query(
            type: $this->name,
            from: $this->from,
            value: str_replace('-', ' ', $this->value),
            to: $this->to
        );

        return view('analytics::livewire.breakdown', [
            'data' => $data['data'] ?? [],
            'first' => $data['first'] ?? null,
            'last' => $data['last'] ?? null,
            'total' => $data['total'] ?? 0,
            'page' => 'breakdown',
            'range' => $this->range,
        ]);
    }
}
