<x-app>
    <div class="bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800 flex-grow min-h-screen">
        <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-3xl">
                <div class="pt-4 px-4">
                    <div class="flex items-center justify-between">
                        @if(isset($website))
                            <h1 class="group relative text-4xl font-bold bg-clip-text text-black dark:text-white transition-all duration-500 cursor-pointer">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-400 rounded-xl blur opacity-50 group-hover:opacity-75 transition-opacity duration-300"></div>
                                        <img src="https://www.google.com/s2/favicons?domain={{ $website->domain }}" 
                                             alt="{{ $website->domain }} favicon"
                                             class="relative w-10 h-10 rounded-xl shadow-xl transform group-hover:scale-110 transition-all duration-300" />
                                    </div>
                                    <span class="relative inline-block">
                                        {{ $website->domain }}
                                        <span class="absolute -bottom-1 left-0 w-full h-1 rounded-full bg-gradient-to-r from-blue-400 to-purple-400 transform origin-left scale-x-0 group-hover:scale-x-100 transition-transform duration-500 ease-out"></span>
                                    </span>
                                </div>
                            </h1>
                        @endif
                        @if(isset($website))
                           

                            <div class="flex space-x-4">
                                <div x-cloak x-data="{ open: false }" class="relative">
                                    @if(request()->is('dashboard') || request()->is('analytics/*'))
                                        <button @click="open = !open" class="btn bg-gradient-to-r from-blue-500 to-blue-600 text-white border-2 border-blue-700 rounded-lg px-4 py-2 font-bold uppercase tracking-widest shadow-inner hover:from-blue-600 hover:to-blue-700 active:translate-y-px">
                                            <span class="flex items-center">
                                                <x-icon name="heroicon-o-cog" class="w-5 h-5 mr-2" />
                                                <span>{{ __('Settings') }}</span>
                                            </span>
                                        </button>
                                    @endif

                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-4 w-56 bg-white dark:bg-gray-800 rounded-xl border-4 border-indigo-500 shadow-2xl z-10 transition-all duration-300 transform origin-top-right" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                                        <div class="py-2">
                                            @if(Auth::check() && $website->team_id == Auth::user()->current_team_id)
                                                <a href="{{ route('analytics.sites.edit', $website->id) }}" class="group flex items-center px-6 py-3 text-base font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500 hover:text-white transition-colors duration-200">
                                                    <x-icon name="heroicon-o-pencil" class="w-5 h-5 mr-3 group-hover:animate-bounce" />
                                                    {{ __('Edit') }}
                                                </a>
                                            @endif
                                            @isset($range)
                                                <a href="{{ route('analytics.stats.overview', ['website' => $website, 'from' => $range['from'] ?? null, 'to' => $range['to'] ?? null]) }}" class="group flex items-center px-6 py-3 text-base font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500 hover:text-white transition-colors duration-200">
                                                    <x-icon name="heroicon-o-eye" class="w-5 h-5 mr-3 group-hover:animate-pulse" />
                                                    {{ __('View') }}
                                                </a>
                                            @endisset

                                            <a href="{{ 'http://' . $website->domain }}" target="_blank" rel="nofollow noreferrer noopener" class="group flex items-center px-6 py-3 text-base font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-500 hover:text-white transition-colors duration-200">
                                                <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-5 h-5 mr-3 group-hover:animate-bounce" />
                                                {{ __('Open') }}
                                            </a>

                                            @if(Auth::check() && $website->team_id == Auth::user()->current_team_id)
                                                <div class="border-t-4 border-indigo-200 dark:border-indigo-700 my-2"></div>

                                                <button @click="$dispatch('open-modal', { 
                                                    action: '{{ route('analytics.sites.destroy', $website->id) }}',
                                                    title: '{{ __('Delete') }}',
                                                    text: '{{ __('Are you sure you want to delete :name?', ['name' => $website->domain]) }}',
                                                    button: 'bg-red-500 hover:bg-red-600 text-white dark:bg-red-600 dark:hover:bg-red-700'
                                                })" class="group flex items-center w-full px-6 py-3 text-base font-bold text-red-600 dark:text-red-400 hover:bg-red-500 hover:text-white transition-colors duration-200">
                                                    <x-icon name="heroicon-o-trash" class="w-5 h-5 mr-3 group-hover:animate-bounce" />
                                                    {{ __('Delete') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(isset($website))
                    <div x-cloak class="flex mt-3" x-data="{ openMenu: false }">
                        <nav class="w-full p-2 bg-gradient-to-r from-blue-600 to-purple-700 rounded-xl shadow-lg border-2 border-blue-300 dark:border-purple-700">
                            <div class="flex flex-grow" id="stats-navbar">
                                <ul class="flex flex-row justify-around w-full">
                                    @php
                                        $menuItems = [
                                            'realtime' => ['icon' => '<div class="absolute w-3 h-3 bg-green-400 rounded-full animate-ping"></div>', 'label' => __('Realtime'), 'route' => 'analytics.stats.realtime'],
                                            'overview' => ['icon' => 'heroicon-o-chart-bar', 'label' => __('Overview'), 'route' => 'analytics.stats.overview'],
                                            'behavior' => [
                                                'icon' => 'heroicon-o-globe-alt',
                                                'label' => __('Behavior'),
                                                'submenu' => [
                                                    ['label' => __('Pages'), 'route' => 'analytics.stats.pages', 'website' => $website],
                                                    ['label' => __('Landing pages'), 'route' => 'analytics.stats.landing_pages']
                                                ]
                                            ],
                                            'acquisitions' => [
                                                'icon' => 'heroicon-o-globe-alt',
                                                'label' => __('Acquisitions'),
                                                'submenu' => [
                                                    ['label' => __('Referrers'), 'route' => 'analytics.stats.referrers'],
                                                    ['label' => __('Search engines'), 'route' => 'analytics.stats.search_engines'],
                                                    ['label' => __('Social networks'), 'route' => 'analytics.stats.social_networks'],
                                                    ['label' => __('Campaigns'), 'route' => 'analytics.stats.campaigns']
                                                ]
                                            ],
                                            'geographic' => [
                                                'icon' => 'heroicon-o-globe-alt',
                                                'label' => __('Geographic'),
                                                'submenu' => [
                                                    ['label' => __('Continents'), 'route' => 'analytics.stats.continents'],
                                                    ['label' => __('Countries'), 'route' => 'analytics.stats.countries'],
                                                    ['label' => __('Cities'), 'route' => 'analytics.stats.cities'],
                                                    ['label' => __('Languages'), 'route' => 'analytics.stats.languages']
                                                ]
                                            ],
                                            'technology' => [
                                                'icon' => 'heroicon-o-globe-alt',
                                                'label' => __('Technology'),
                                                'submenu' => [
                                                    ['label' => __('Operating systems'), 'route' => 'analytics.stats.operating_systems'],
                                                    ['label' => __('Browsers'), 'route' => 'analytics.stats.browsers'],
                                                    ['label' => __('Screen resolutions'), 'route' => 'analytics.stats.screen_resolutions'],
                                                    ['label' => __('Devices'), 'route' => 'analytics.stats.devices']
                                                ]
                                            ],
                                            'events' => ['icon' => 'heroicon-o-globe-alt', 'label' => __('Events'), 'route' => 'analytics.stats.events']
                                        ];
                                    @endphp

                                    @foreach($menuItems as $key => $item)
                                        <li class="nav-item relative" 
                                            @if(isset($item['submenu'])) 
                                                x-data="{ open: false, timeout: null }" 
                                                @mouseenter="openMenu = '{{ $key }}'; clearTimeout(timeout)" 
                                                @mouseleave="timeout = setTimeout(() => { openMenu = null }, 300)"
                                            @endif>
                                            @if(isset($item['submenu']))
                                                <button class="flex items-center font-bold text-white py-3 px-4 rounded-lg transition-all duration-300 hover:bg-white hover:bg-opacity-20" :class="{ 'bg-white bg-opacity-20': openMenu === '{{ $key }}' }">
                                            @else
                                                <a href="{{ route($item['route'], ['website' => $website]) }}" 
                                                class="flex items-center font-bold text-white py-3 px-4 rounded-lg transition-all duration-300 hover:bg-white hover:bg-opacity-20 {{ Route::currentRouteName() == $item['route'] ? 'bg-white bg-opacity-20 shadow-inner' : '' }}">
                                            @endif
                                                <span class="flex items-center relative w-5 h-5 mr-3">
                                                    @if($key === 'realtime')
                                                        {!! $item['icon'] !!}
                                                    @else
                                                        <x-icon :name="$item['icon']" class="w-5 h-5 text-white" />
                                                    @endif
                                                </span>
                                                <span class="tracking-wide">{{ $item['label'] }}</span>
                                                @if(isset($item['submenu']))
                                                    <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 ml-2 text-white" />
                                                @endif
                                            @if(isset($item['submenu']))
                                                </button>
                                            @else
                                                </a>
                                            @endif

                                            @if(isset($item['submenu']))
                                                <div x-show="openMenu === '{{ $key }}'" 
                                                    @mouseenter="clearTimeout(timeout)"
                                                    @mouseleave="openMenu = null"
                                                    class="absolute z-10 mt-2 w-48 rounded-lg shadow-xl bg-gradient-to-b from-blue-500 to-purple-600 border-2 border-white border-opacity-20">
                                                    <div class="py-2" role="menu" aria-orientation="vertical">
                                                        @foreach($item['submenu'] as $subItem)
                                                            <a class="block px-4 py-2 text-sm font-medium text-white hover:bg-white hover:bg-opacity-20 transition-all duration-200 {{ Route::currentRouteName() == $subItem['route'] ? 'bg-white bg-opacity-20' : '' }}" 
                                                            href="{{ route($subItem['route'], ['website' => $website]) }}" 
                                                            role="menuitem">{{ $subItem['label'] }}</a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </nav>
                    </div>
                    @endif
                </div>

                <div class=" min-h-[500px]">
                    <div class="p-6">
                        {{ $slot }}
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 flex justify-between items-center">
                        <span>
                            {{ __('Report generated on :date at :time (UTC :offset).', [
                                'date' => now()->format(__('Y-m-d')),
                                'time' => now()->format('H:i:s'),
                                'offset' => now()->getOffsetString()
                            ]) }}
                        </span>
                        <a href="{{ Request::fullUrl() }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200">{{ __('Refresh report') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app>