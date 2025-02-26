<?php

namespace Shaferllc\Analytics\Livewire;

use App\Models\Site;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

#[Title('Debug')]
class Debug extends Component
{
    use WithPagination;

    #[Locked]
    public Site $site;

    public array $newLogIds = [];
    public bool $autoRefresh = true;
    public array $filters = [
        'levels' => ['info', 'warning', 'error', 'debug']
    ];

    #[Computed]
    public function logs()
    {
        return $this->site->debug()
            ->when($this->filters['levels'], function ($query) {
                $query->whereIn('level', $this->filters['levels']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(100);
    }

    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function refreshData()
    {
        // Get the latest logs
        $latestLogs = $this->site->debug()
            ->where('created_at', '>', now()->subSeconds(10))
            ->pluck('id')
            ->toArray();

        // Update new log IDs for highlighting
        $this->newLogIds = $latestLogs;

        // Only refresh if there are new logs
        if (count($latestLogs) > 0) {
            $this->logs = $this->logs();
        }
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    #[On('debug-log-created')]
    public function handleNewLog($logId)
    {
        if ($this->autoRefresh) {
            $this->newLogIds[] = $logId;
            $this->logs = $this->logs();
        }
    }

    public function render()
    {
        return view('analytics::livewire.debug');
    }
}
