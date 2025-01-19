<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use App\Models\Site;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Shaferllc\Analytics\Traits\ComponentTrait;

#[Title('Geographic')]
class Geographic extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;


    public function render()
    {
        $continents = $this->query(
            type: 'continent',
            from: $this->from,
            to: $this->to
        );

        $countries = $this->query(
            type: 'country',
            from: $this->from,
            to: $this->to
        );

        $cities = $this->query(
            type: 'city',
            from: $this->from,
            to: $this->to
        );

        $languages = $this->query(
            type: 'language',
            from: $this->from,
            to: $this->to
        );

        $timezones = $this->query(
            type: 'timezone',
            from: $this->from,
            to: $this->to
        );

        return view('analytics::livewire.geographic', [
            'continentsTotal' => $continents['total'] ?? 0,
            'countriesTotal' => $countries['total'] ?? 0,
            'citiesTotal' => $cities['total'] ?? 0,
            'languagesTotal' => $languages['total'] ?? 0,
            'timezonesTotal' => $timezones['total'] ?? 0,
        ]);
    }
}
