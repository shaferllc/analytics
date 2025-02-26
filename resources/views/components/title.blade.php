@props([
    'actions' => null,
    'daterange' => null,
    'description' => null,
    'filters' => null,
    'from' => null,
    'icon' => null,
    'data' => null,
    'page' => null,
    'paginator' => null,
    'perPage' => null,
    'search' => null,
    'sort' => null,
    'sortBy' => null,
    'subtitle' => null,
    'sortWords' => [],
    'title' => null,
    'total' => null,
    'totalText' => null,
    'color' => null,
    'to' => null,
])

<header class="bg-white dark:bg-gray-800 shadow-sm">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-4">
                    @if($icon)
                        <div class="flex-shrink-0">
                            <div class="bg-gradient-to-br from-gray-50 dark:from-gray-700 to-transparent p-3 rounded-lg shadow-sm">
                                <x-icon :name="$icon" class="w-8 h-8 text-gray-600 dark:text-gray-300" />
                            </div>
                        </div>
                    @endif

                    <div class="space-y-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $title }}
                        </h1>

                        @if($subtitle)
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $subtitle }}
                            </p>
                        @endif

                        @if($total)
                            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                <x-icon name="heroicon-o-eye" class="w-4 h-4" />
                                <span>{{ number_format($total, 0, __('.'), __(',')) }} {{ $totalText }}</span>
                            </div>
                        @endif

                        @if($daterange)
                            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                <x-icon name="heroicon-o-calendar" class="w-4 h-4" />
                                <span>{{ \Carbon\Carbon::parse($from)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M j, Y') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                @if($filters)
                    <div class="flex items-center space-x-2">
                        {{ $filters }}
                    </div>
                @endif

                @if($sortBy)
                    <div class="relative">
                        <button
                            @click="sortMenuOpen = !sortMenuOpen"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <x-icon name="heroicon-o-arrows-up-down" class="w-5 h-5 mr-2" />
                            {{ __('Sort by') }}
                        </button>

                        <div
                            x-show="sortMenuOpen"
                            @click.away="sortMenuOpen = false"
                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <div class="py-1">
                                <button
                                    wire:click="$set('sort', '{{ $sort === 'desc' ? 'asc' : 'desc' }}')"
                                    class="w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600"
                                >
                                    <x-icon
                                        name="{{ $sort === 'desc' ? 'heroicon-o-arrow-down' : 'heroicon-o-arrow-up' }}"
                                        class="w-5 h-5 mr-2"
                                    />
                                    {{ $sort === 'desc' ? __('Descending') : __('Ascending') }}
                                </button>
                            </div>

                            <div class="py-1">
                                <button
                                    wire:click="$set('sortBy', 'count')"
                                    class="w-full px-4 py-2 text-sm {{ $sortBy === 'count' ? 'bg-indigo-100 dark:bg-indigo-600 text-indigo-900 dark:text-indigo-100' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600' }}"
                                >
                                    <x-icon
                                        name="heroicon-o-calculator"
                                        class="w-5 h-5 mr-2"
                                    />
                                    {{ $sortWords['count'] }}
                                </button>

                                <button
                                    wire:click="$set('sortBy', 'value')"
                                    class="w-full px-4 py-2 text-sm {{ $sortBy === 'value' ? 'bg-indigo-100 dark:bg-indigo-600 text-indigo-900 dark:text-indigo-100' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600' }}"
                                >
                                    <x-icon
                                        name="heroicon-o-bars-4"
                                        class="w-5 h-5 mr-2"
                                    />
                                    {{ $sortWords['value'] }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="heroicon-o-magnifying-glass" class="w-5 h-5 text-gray-400" />
                        </div>
                        <input
                            x-ref="searchInput"
                            type="text"
                            wire:model.live.debounce.500ms="search"
                            placeholder="{{ __('Search...') }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200"
                        >
                    </div>
                @endif

                @if($paginator && $paginator->hasPages())
                    <div class="relative">
                        <button
                            @click="perPageMenuOpen = !perPageMenuOpen"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <x-icon name="heroicon-o-list-bullet" class="w-5 h-5 mr-2" />
                            {{ $perPage }} {{ __('per page') }}
                        </button>

                        <div
                            x-show="perPageMenuOpen"
                            @click.away="perPageMenuOpen = false"
                            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <div class="py-1">
                                @foreach([12, 24, 48, 96, 192] as $value)
                                    <button
                                        wire:click="$set('perPage', {{ $value }})"
                                        class="w-full px-4 py-2 text-sm {{ $perPage === $value ? 'bg-indigo-100 dark:bg-indigo-600 text-indigo-900 dark:text-indigo-100' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600' }}"
                                    >
                                        <x-icon
                                            name="{{ $perPage === $value ? 'heroicon-o-check' : 'heroicon-o-minus' }}"
                                            class="w-5 h-5 mr-2"
                                        />
                                        {{ $value }} {{ __('per page') }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if($actions)
                    <div class="flex items-center space-x-2">
                        @foreach($actions as $action)
                            <a
                                href="{{ $action['url'] }}"
                                x-data="{ tooltip: false }"
                                @mouseenter="tooltip = true"
                                @mouseleave="tooltip = false"
                                class="relative inline-flex justify-center rounded-md border border-gray-50 dark:border-gray-500 shadow-sm px-5 py-3 bg-white dark:bg-gray-600 text-base font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-blue-500/30"
                            >
                                <x-icon :name="$action['icon']" class="w-5 h-5 mr-2" />
                                {{ $action['label'] }}

                                <div
                                    x-show="tooltip"
                                    x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-1"
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 text-sm text-white bg-black rounded-lg whitespace-nowrap"
                                >
                                    {{ $action['tooltip'] }}
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($daterange)
                    <x-analytics::datepicker :daterange="$daterange" :from="$from" :to="$to" />
                @endif
            </div>
        </div>

        <x-loading />

        @if(session()->has('success'))
            <div class="rounded-md bg-green-100 dark:bg-green-900/50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="heroicon-o-check-circle" class="h-5 w-5 text-green-600 dark:text-green-400"/>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="rounded-md bg-red-100 dark:bg-red-900/50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <x-icon name="heroicon-o-x-circle" class="h-5 w-5 text-red-600 dark:text-red-400"/>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{ $slot }}
    </div>
</header>
