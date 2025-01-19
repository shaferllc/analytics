<div x-cloak x-data="{
    open: false,
    init() {
        window.addEventListener('keydown', e => {
            // Alt + D to open/close datepicker
            if (e.altKey && e.key === 'd') {
                e.preventDefault();
                this.open = !this.open;
            }

            if (this.open) {
                // Escape to close
                if (e.key === 'Escape') {
                    this.open = false;
                }

                // Keyboard shortcuts for common ranges
                if (e.altKey) {
                    switch(e.key) {
                        case 't': // Today
                            $wire.set('daterange', 'today');
                            this.open = false;
                            break;
                        case 'y': // Yesterday
                            $wire.set('daterange', 'yesterday');
                            this.open = false;
                            break;
                        case '7': // Last 7 days
                            $wire.set('daterange', 'last7days');
                            this.open = false;
                            break;
                        case '3': // Last 30 days
                            $wire.set('daterange', 'last30days');
                            this.open = false;
                            break;
                        case 'm': // This month
                            $wire.set('daterange', 'thisMonth');
                            this.open = false;
                            break;
                    }
                }
            }
        });
    }
}" class="relative inline-block text-left">
    <div>
        <button @click="open = !open" type="button" class="border border-gray-200 dark:border-gray-400/20 inline-flex justify-center w-full rounded-md shadow-sm px-5 py-3 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-blue-500/10" id="menu-button" aria-expanded="true" aria-haspopup="true" title="{{ __('Select Date Range (Alt+D)') }}">
            <x-icon name="heroicon-o-calendar" class="-ml-1 mr-3 h-6 w-6 text-gray-400" />
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
            <span class="flex flex-col items-start">
                <span>{{ __($ranges[$daterange]) }}</span>
            </span>
            <x-icon name="heroicon-o-chevron-down" class="-mr-1 ml-3 h-6 w-6 text-gray-400" />
        </button>
    </div>

    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="z-50 origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none divide-y divide-gray-200 dark:divide-gray-700" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
        <div class="px-5 py-4" role="none">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start-date" class="block text-base font-medium text-gray-700 dark:text-gray-300">
                        <span class="inline-flex items-center">
                            <x-icon name="heroicon-o-calendar-days" class="w-4 h-4 mr-1" />
                            {{ __('Start Date') }}
                        </span>
                    </label>
                    <input type="date" wire:model.lazy="from" id="start-date" class="mt-2 block w-full bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-base p-2" title="{{ __('Select Start Date') }}">
                </div>
                <div>
                    <label for="end-date" class="block text-base font-medium text-gray-700 dark:text-gray-300">
                        <span class="inline-flex items-center">
                            <x-icon name="heroicon-o-calendar-days" class="w-4 h-4 mr-1" />
                            {{ __('End Date') }}
                        </span>
                    </label>
                    <input type="date" format="YYYY-MM-DD" wire:model.lazy="to" id="end-date" class="mt-2 block w-full bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-md shadow-sm text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-base p-2" title="{{ __('Select End Date') }}">
                </div>
            </div>
        </div>
        <div class="py-2" role="none">
            @foreach(['today' => 'Today', 'yesterday' => 'Yesterday', 'last7days' => 'Last 7 days', 'last30days' => 'Last 30 days', 'thisMonth' => 'This month', 'lastMonth' => 'Last month', 'last3Months' => 'Last 3 months', 'last6Months' => 'Last 6 months', 'thisYear' => 'This year', 'lastYear' => 'Last year', 'all' => 'All time'] as $range => $label)
                <button wire:click="$set('daterange', '{{ $range }}'); open = false;" class="text-left w-full flex items-center px-5 py-3 text-base {{ $daterange === $range ? 'bg-blue-500 dark:bg-blue-800 text-white dark:text-blue-100 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}" role="menuitem" title="{{ __('Select :range', ['range' => $label]) }} {{ $range === 'today' ? '(Alt+T)' : ($range === 'yesterday' ? '(Alt+Y)' : ($range === 'last7days' ? '(Alt+7)' : ($range === 'last30days' ? '(Alt+3)' : ($range === 'thisMonth' ? '(Alt+M)' : '')))) }}">
                    <span class="flex-grow">
                        <span class="inline-flex items-center">
                            <x-icon name="{{ $range === 'today' ? 'heroicon-o-sun' : ($range === 'yesterday' ? 'heroicon-o-clock' : 'heroicon-o-calendar') }}" class="w-4 h-4 mr-2" />
                            {{ __($label) }}
                        </span>
                    </span>
                    @if($daterange === $range)
                        <x-icon name="heroicon-o-check" class="h-6 w-6 text-blue-400" />
                    @endif
                </button>
            @endforeach
        </div>
    </div>
</div>
