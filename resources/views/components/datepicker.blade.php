<div x-cloak x-data="{
    open: false,
    init() {
        window.addEventListener('keydown', e => {
            if (e.altKey && e.key === 'd') {
                e.preventDefault();
                this.open = !this.open;
            }

            if (this.open) {
                if (e.key === 'Escape') this.open = false;

                if (e.altKey) {
                    switch(e.key) {
                        case 't': $wire.set('daterange', 'today'); this.open = false; break;
                        case 'y': $wire.set('daterange', 'yesterday'); this.open = false; break;
                        case '7': $wire.set('daterange', 'last7days'); this.open = false; break;
                        case '3': $wire.set('daterange', 'last30days'); this.open = false; break;
                        case 'm': $wire.set('daterange', 'thisMonth'); this.open = false; break;
                    }
                }
            }
        });
    }
}" class="relative inline-block text-left">
    <div>
        <button @click="open = !open" type="button" class="inline-flex justify-center w-full rounded-lg shadow-sm px-4 py-2.5 bg-white/90 dark:bg-slate-800/90 border border-slate-200/60 dark:border-slate-700/60 text-sm font-medium text-slate-900 dark:text-slate-100 hover:bg-white/50 dark:hover:bg-slate-700/30 transition-colors" id="menu-button" aria-expanded="true" aria-haspopup="true" title="{{ __('Select Date Range (Alt+D)') }}">
            <x-icon name="heroicon-o-calendar" class="w-5 h-5 text-slate-400 mr-2" />
            @php
                $ranges = [
                    'today' => 'Today',
                    'yesterday' => 'Yesterday',
                    'last7days' => 'Last 7 days',
                    'last30days' => 'Last 30 days',
                    'thisMonth' => 'This month',
                    'lastMonth' => 'Last month',
                    'last3Months' => 'Last 3 months',
                    'last6Months' => 'Last 6 months',
                    'thisYear' => 'This year',
                    'lastYear' => 'Last year',
                    'all' => 'All time'
                ];
            @endphp
            <span>{{ __($ranges[$daterange]) }}</span>
            <x-icon name="heroicon-o-chevron-down" class="w-5 h-5 text-slate-400 ml-2" />
        </button>
    </div>

    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="z-50 origin-top-right absolute right-0 mt-2 w-80 rounded-xl shadow-lg bg-white/90 dark:bg-slate-800/90 border border-slate-200/60 dark:border-slate-700/60 divide-y divide-slate-200/60 dark:divide-slate-700/60" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
        <div class="px-4 py-3" role="none">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="start-date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        <x-icon name="heroicon-o-calendar-days" class="w-4 h-4 mr-1 inline" />
                        {{ __('Start Date') }}
                    </label>
                    <input type="date" wire:model.lazy="from" id="start-date" class="block w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-700/60 rounded-lg shadow-sm text-slate-900 dark:text-slate-100 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                </div>
                <div>
                    <label for="end-date" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        <x-icon name="heroicon-o-calendar-days" class="w-4 h-4 mr-1 inline" />
                        {{ __('End Date') }}
                    </label>
                    <input type="date" wire:model.lazy="to" id="end-date" class="block w-full bg-white dark:bg-slate-900 border border-slate-200/60 dark:border-slate-700/60 rounded-lg shadow-sm text-slate-900 dark:text-slate-100 focus:ring-blue-500 focus:border-blue-500 text-sm p-2">
                </div>
            </div>
        </div>
        <div class="py-1" role="none">
            @foreach(['today' => 'Today', 'yesterday' => 'Yesterday', 'last7days' => 'Last 7 days', 'last30days' => 'Last 30 days', 'thisMonth' => 'This month', 'lastMonth' => 'Last month', 'last3Months' => 'Last 3 months', 'last6Months' => 'Last 6 months', 'thisYear' => 'This year', 'lastYear' => 'Last year', 'all' => 'All time'] as $range => $label)
                <button wire:click="$set('daterange', '{{ $range }}'); open = false;" class="w-full flex items-center px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100/50 dark:hover:bg-slate-700/30 transition-colors {{ $daterange === $range ? 'bg-blue-50 dark:bg-blue-900/20 font-medium' : '' }}" role="menuitem">
                    <x-icon name="{{ $range === 'today' ? 'heroicon-o-sun' : ($range === 'yesterday' ? 'heroicon-o-clock' : 'heroicon-o-calendar') }}" class="w-4 h-4 mr-2 text-slate-400" />
                    <span class="flex-grow">{{ __($label) }}</span>
                    @if($daterange === $range)
                        <x-icon name="heroicon-o-check" class="w-5 h-5 text-blue-500" />
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>
