<?php

namespace Shaferllc\Analytics\Livewire;

use Livewire\Component;
use VisualAppeal\SslLabs;
use App\Services\DnsService;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Spatie\Dns\Dns as SpatieDns;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Log;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Traits\ComponentTrait;
use Shaferllc\Analytics\Traits\DateRangeTrait;

class Dns extends Component
{
    use DateRangeTrait, WithPagination;

    #[Locked]
    public Site $site;

    public string $search = '';

    public string $sortBy = 'domain';
    public string $filter = 'all';
    public bool $sortDesc = false;

    public array $dns = [];

    public function mount(Site $site)
    {
        $this->site = $site;

        $this->dns = (new DnsService())->getRecords($this->site);

        dd($this->dns);
    }

    public function sort()
    {
        $this->sortDesc = !$this->sortDesc;
    }

    #[Computed]
    public function dns()
    {
        // return $this->site->dns()
        //     ->when($this->search, function($query) {
        //         return $query->where('domain', 'like', '%' . $this->search . '%')
        //             ->orWhere('issuer', 'like', '%' . $this->search . '%');
        //     })
        //     ->when($this->filter !== 'all', function($query) {
        //         return match($this->filter) {
        //             'valid' => $query->where('was_valid', true)
        //                 ->where('valid_to', '>', now()->addDays(30)),
        //             'expiring' => $query->where('was_valid', true)
        //                 ->where('valid_to', '<=', now()->addDays(30))
        //                 ->where('valid_to', '>', now()),
        //             'expired' => $query->where('was_valid', false)
        //                 ->orWhere('valid_to', '<=', now()),
        //             default => $query
        //         };
        //     })
        //     ->orderBy($this->sortBy, $this->sortDesc ? 'desc' : 'asc')
        //     ->paginate(10);
    }

    #[Layout('analytics::layouts.app')]
    public function render()
    {
        return view('analytics::livewire.dns', [
            'website' => $this->site,
            'dns' => $this->dns(),
        ])->with([
            'page' => 'dns',
            'website' => $this->site
        ]);
    }
}
