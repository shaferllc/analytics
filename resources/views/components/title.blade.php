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

<header
    x-data="{
        sortMenuOpen: false,
        perPageMenuOpen: false,

        initKeyboardShortcuts() {
            window.addEventListener('keydown', (e) => {
                // Only trigger if not typing in an input/textarea
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    return;
                }

                // Ctrl/Cmd + f to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    this.$refs.searchInput.focus();
                }

                // Ctrl/Cmd + S to toggle sort menu
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    this.sortMenuOpen = !this.sortMenuOpen;
                }

                // Ctrl/Cmd + P to toggle per page menu
                if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                    e.preventDefault();
                    this.perPageMenuOpen = !this.perPageMenuOpen;
                }
            });
        }
    }"
    x-init="initKeyboardShortcuts()"
    class="bg-white dark:bg-gray-600 rounded-xl shadow-lg border border-gray-50 dark:border-gray-500 transition-transform duration-200"
>
    <div class="mx-auto p-4 space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
                @if($icon)
                    <div class="relative">
                        <div class="absolute inset-0 bg-gray-500/20 animate-pulse rounded-full"></div>
                        <div class="bg-gradient-to-br from-gray-50 dark:from-gray-800 to-transparent backdrop-blur-lg p-3 rounded-full shadow-lg hover:scale-110 transition-transform duration-300 hover:rotate-12">
                            <x-icon
                                :name="$icon"
                                class="w-8 h-8 text-gray-700 dark:text-gray-50 animate-spin-slow hover:animate-spin"
                            />
                        </div>
                    </div>
                @endif

                <div class="space-y-2">
                    <x-tooltip :text="$description" class="text-2xl font-bold text-gray-700 dark:text-gray-50">
                        <h1 class="text-2xl font-bold text-gray-700 dark:text-gray-50">
                            {{ $title }}
                        </h1>
                    </x-tooltip>

                    @if($subtitle)
                        <p class="text-lg text-gray-500 dark:text-gray-300">
                            {{ $subtitle }}
                        </p>
                    @endif

                    @if($total)
                        <p class="text-gray-500 dark:text-gray-300">
                            <span class="inline-flex items-center">
                                <x-icon name="heroicon-o-eye" class="w-4 h-4 mr-2" />
                                {{ number_format($total, 0, __('.'), __(',')) }} {{ $totalText }}
                            </span>
                        </p>
                    @endif

                    @if($daterange)
                        <p class="text-gray-500 dark:text-gray-300">
                            <span class="inline-flex items-center">
                                <x-icon name="heroicon-o-calendar" class="w-4 h-4 mr-2" />
                                <span class="text-sm">
                                    {{ \Carbon\Carbon::parse($from)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M j, Y') }}
                                </span>
                            </span>
                        </p>
                    @endif
                </div>
            </div>

            <div class="flex items-center space-x-6">
                @if($filters)
                    <div class="flex items-center space-x-3">
                        {{ $filters }}
                    </div>
                @endif

                @if($sortBy)
                    <div x-cloak class="relative inline-block text-left">
                        <button
                            @click="sortMenuOpen = !sortMenuOpen"
                            type="button"
                            class="border border-gray-200 dark:border-gray-400/20 inline-flex justify-center w-full rounded-md shadow-sm px-5 py-3 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-blue-500/10"
                        >
                            <x-icon name="heroicon-o-arrows-up-down" class="h-5 w-5 text-gray-500 dark:text-gray-300" />
                            <span class="mx-3">{{ __('Sort by') }}</span>
                            <x-icon name="heroicon-o-chevron-down" class="h-5 w-5 text-gray-500 dark:text-gray-300" />
                        </button>

                        <div
                            x-show="sortMenuOpen"
                            @click.away="sortMenuOpen = false"
                            class="z-10 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-600 ring-1 ring-black ring-opacity-5 divide-y divide-gray-50 dark:divide-gray-500"
                        >
                            <div class="py-2">
                                <button
                                    wire:click="$set('sort', '{{ $sort === 'desc' ? 'asc' : 'desc' }}')"
                                    class="flex items-center w-full px-4 py-2 text-sm text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-500"
                                >
                                    <x-icon
                                        name="{{ $sort === 'desc' ? 'heroicon-o-arrow-down' : 'heroicon-o-arrow-up' }}"
                                        class="mr-3 h-5 w-5 text-gray-500 dark:text-gray-300"
                                    />
                                    {{ $sort === 'desc' ? __('Descending') : __('Ascending') }}
                                </button>
                            </div>

                            @if($sortBy)
                                <div class="py-2">
                                    <button
                                        wire:click="$set('sortBy', 'count')"
                                        class="flex items-center w-full px-4 py-2 text-sm {{ $sortBy === 'count' ? 'bg-blue-100 dark:bg-blue-800 text-blue-900 dark:text-blue-100' : 'text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-500' }}"
                                    >
                                        <x-icon
                                            name="heroicon-o-calculator"
                                            class="mr-3 h-5 w-5 {{ $sortBy === 'count' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-300' }}"
                                        />
                                        {{ $sortWords['count'] }}
                                    </button>

                                    <button
                                        wire:click="$set('sortBy', 'value')"
                                        class="flex items-center w-full px-4 py-2 text-sm {{ $sortBy === 'value' ? 'bg-blue-100 dark:bg-blue-800 text-blue-900 dark:text-blue-100' : 'text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-500' }}"
                                    >
                                        <x-icon
                                            name="heroicon-o-bars-4"
                                            class="mr-3 h-5 w-5 {{ $sortBy === 'value' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-300' }}"
                                        />
                                        {{ $sortWords['value'] }}
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="heroicon-o-magnifying-glass" class="h-5 w-5 text-gray-500 dark:text-gray-300"/>
                        </div>
                        <input
                            x-ref="searchInput"
                            type="text"
                            wire:model.live.debounce.500ms="search"
                            placeholder="{{ __('Search...') }}"
                            class="focus:border-transparent focus:border-1 placeholder:text-gray-500 dark:placeholder:text-gray-300 inline-flex justify-center w-full rounded-md border pl-10 border-gray-200 dark:border-gray-400/20 shadow-sm px-5 py-3 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-blue-500/10"
                        >
                    </div>
                @endif

                @if($paginator && $paginator->hasPages())
                    <div x-cloak class="relative inline-block text-left">
                        <button
                            @click="perPageMenuOpen = !perPageMenuOpen"
                            type="button"
                            class="border border-gray-200 dark:border-gray-400/20 inline-flex justify-center w-full rounded-md shadow-sm px-5 py-3 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 dark:focus:ring-offset-gray-800 focus:ring-blue-500/10"
                        >
                            <x-icon name="heroicon-o-list-bullet" class="h-5 w-5 text-gray-500 dark:text-gray-300" />
                            <span class="mx-3">{{ $perPage }} {{ __('per page') }}</span>
                            <x-icon name="heroicon-o-chevron-down" class="h-5 w-5 text-gray-500 dark:text-gray-300" />
                        </button>

                        <div
                            x-show="perPageMenuOpen"
                            @click.away="perPageMenuOpen = false"
                            class="z-10 origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-600 ring-1 ring-black ring-opacity-5"
                        >
                            <div class="py-2">
                                @foreach([12, 24, 48, 96, 192] as $value)
                                    <button
                                        wire:click="$set('perPage', {{ $value }})"
                                        class="flex items-center w-full px-4 py-2 text-sm {{ $perPage === $value ? 'bg-blue-100 dark:bg-blue-800 text-blue-900 dark:text-blue-100' : 'text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-500' }}"
                                    >
                                        <x-icon
                                            name="{{ $perPage === $value ? 'heroicon-o-check' : 'heroicon-o-minus' }}"
                                            class="mr-3 h-5 w-5 {{ $perPage === $value ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-300' }}"
                                        />
                                        {{ $value }} {{ __('per page') }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if($actions)
                    <div class="flex items-center space-x-3">
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
