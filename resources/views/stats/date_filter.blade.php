<div x-data="{ open: false }" class="relative inline-block text-left">
    <button @click="open = !open" type="button" class="inline-flex justify-center items-center w-full rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
        <svg class="h-5 w-5 text-gray-400 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
        </svg>
        <span>{{ $this->getFormattedDateRange() }}</span>
        <svg class="h-5 w-5 text-gray-400 ml-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg> 
    </button>

    <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
        <div class="p-4" role="none">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="start-date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Start Date') }}</label>
                    <input type="date" wire:model.live="from" id="start-date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
                </div>
                <div>
                    <label for="end-date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('End Date') }}</label>
                    <input type="date" wire:model.live="to" id="end-date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 transition duration-150 ease-in-out">
                </div>
            </div>
        </div>
        <div class="py-1" role="none">
            @foreach(['today' => 'Today', 'yesterday' => 'Yesterday', 'last7days' => 'Last 7 days', 'last30days' => 'Last 30 days', 'thisMonth' => 'This month', 'lastMonth' => 'Last month', 'last3Months' => 'Last 3 months', 'last6Months' => 'Last 6 months', 'thisYear' => 'This year', 'lastYear' => 'Last year', 'all' => 'All time'] as $range => $label)
                <a href="#" 
                   wire:click.prevent="updateDateRange('{{ $range }}')" 
                   class="flex items-center px-4 py-2 text-sm {{ $dateRange === $range ? 'bg-blue-100 text-blue-900 font-medium' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }} transition duration-150 ease-in-out" 
                   role="menuitem">
                    <span class="flex-grow">{{ __($label) }}</span>
                    @if($dateRange === $range)
                        <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>