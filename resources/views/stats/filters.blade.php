@if($page == 'realtime')
    <div class="bg-gradient-to-r from-blue-600 to-purple-700 text-white rounded-lg px-4 py-2 shadow-md hover:shadow-lg transition-all duration-300 w-auto inline-block">
        <div class="flex items-center">
            <x-icon name="heroicon-o-clock" class="w-5 h-5 animate-pulse" />
            <span class="ml-2 font-bold text-sm">
                {{ __('Last :seconds seconds', ['seconds' => 60]) }}
            </span>
        </div>
    </div>
@endif

<div class="flex flex-wrap items-center gap-4 mt-4" x-cloak> 
    <div class="flex-grow relative">
        <input class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300" 
               name="search" 
               wire:model.throttle.500ms.live="search" 
               placeholder="{{ __('Search') }}" >
        @if($data->isNotEmpty())
            <div x-data="{ open: false }" class="absolute right-2 top-1/2 transform -translate-y-1/2">
                <button @click="open = !open" class="p-1 text-gray-600 hover:text-gray-800 focus:outline-none">
                    <x-icon name="heroicon-o-adjustments-horizontal" class="w-5 h-5" />
                </button>
                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg z-50 border border-gray-200">
                    <div class="p-3">
                        @if($data->total() > 0)
                            <a @click="$dispatch('open-modal', 'export-modal')" class="btn btn-primary flex items-center justify-center text-sm font-semibold py-1 px-3 rounded">
                                <x-icon name="heroicon-o-arrow-down-tray" class="w-4 h-4 mr-2"/>
                                {{ __('Export') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
    @if($data->total() > 10)
        <select wire:model.live="perPage" id="perPage" class="w-32 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
            @foreach([10, 25, 50, 100] as $value)
                <option value="{{ $value }}">{{ $value }} {{ __('per page') }}</option>
            @endforeach
        </select>
    @endif
</div>
@if($data->isNotEmpty())
    <div class="mt-4 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 p-4 rounded-lg shadow-md">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="text-gray-700 dark:text-gray-300 font-medium">
                {{ __('Total Results:') }} <span class="font-bold text-blue-600 dark:text-blue-400">{{ number_format($data->total()) }}</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @if($search || $sortBy !== 'count' || $sort !== 'desc' || $perPage !== 10 || $from || $to)
                    @if($search)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800 border border-blue-300 shadow-sm">
                            <x-icon name="heroicon-o-magnifying-glass" class="w-4 h-4 mr-1"/>
                            {{ Str::limit($search, 15) }}
                            <button wire:click="$set('search', '')" class="ml-1 text-blue-700 hover:text-blue-900 focus:outline-none">
                                <x-icon name="heroicon-o-x-mark" class="w-4 h-4"/>
                            </button>
                        </span>
                    @endif
                    @if($sortBy !== 'count' || $sort !== 'desc')
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 border border-green-300 shadow-sm">
                            <x-icon name="heroicon-o-arrow-up-down" class="w-4 h-4 mr-1"/>
                            {{ ucfirst($sortBy) }} ({{ $sort }})
                            <button wire:click="resetFilters" class="ml-1 text-green-700 hover:text-green-900 focus:outline-none">
                                <x-icon name="heroicon-o-x-mark" class="w-4 h-4"/>
                            </button>
                        </span>
                    @endif
                    @if($perPage !== 10)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 border border-yellow-300 shadow-sm">
                            <x-icon name="heroicon-o-list-bullet" class="w-4 h-4 mr-1"/>
                            {{ $perPage }}/page
                            <button wire:click="$set('perPage', 10)" class="ml-1 text-yellow-700 hover:text-yellow-900 focus:outline-none">
                                <x-icon name="heroicon-o-x-mark" class="w-4 h-4"/>
                            </button>
                        </span>
                    @endif
                    @if($from || $to)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800 border border-purple-300 shadow-sm">
                            <x-icon name="heroicon-o-calendar" class="w-4 h-4 mr-1"/>
                            {{ $from ? Carbon\Carbon::parse($from)->format('M d, Y') : '' }}
                            -
                            {{ $to ? Carbon\Carbon::parse($to)->format('M d, Y') : '' }}
                        </span>
                    @endif
                @else
                    <span class="text-gray-500 dark:text-gray-400 italic">{{ __('No filters applied') }}</span>
                @endif
            </div>
        </div>
    </div>
@endif
