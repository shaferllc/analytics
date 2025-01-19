<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use App\Models\Site;
use Shaferllc\Analytics\Traits\DateRangeTrait;
use Shaferllc\Analytics\Traits\ComponentTrait;

#[Title('Technology')]
class Technology extends Component
{
    use DateRangeTrait, WithPagination, ComponentTrait;

    #[Locked]
    public Site $site;


    public function render()
    {
        $operatingSystems = $this->query(
            type: 'os',
            from: $this->from,
            to: $this->to
        );

        $browsers = $this->query(
            type: 'browser',
            from: $this->from,
            to: $this->to
        );

        $screenResolutions = $this->query(
            type: 'resolution',
            from: $this->from,
            to: $this->to
        );

        $devices = $this->query(
            type: 'device',
            from: $this->from,
            to: $this->to
        );

        $userAgents = $this->query(
            type: 'user_agent',
            from: $this->from,
            to: $this->to
        );

        return view('analytics::livewire.technology', [
            'website' => $this->site,
            'operatingSystemsTotal' => $operatingSystems['total'] ?? 0,
            'browsersTotal' => $browsers['total'] ?? 0,
            'screenResolutionsTotal' => $screenResolutions['total'] ?? 0,
            'devicesTotal' => $devices['total'] ?? 0,
            'userAgentsTotal' => $userAgents['total'] ?? 0,
        ]);
    }

}
