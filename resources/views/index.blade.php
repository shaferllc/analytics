<x-app>
    <div class="container max-w-7xl m-auto">
        <div class="bg-white flex-grow">
            <a href="{{ route('websites.new') }}" class="btn btn-primary flex justify-center items-center mt-3 md:mt-0">
                <x-icon name="heroicon-o-plus" class="w-4 h-4 fill-current" />{{ __('New website') }}
            </a>
        </div>

        <div class="bg-white">
            <div class="container py-3 my-3">
                <div class="mb-5">
                    <div class="flex flex-col lg:flex-row items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold mb-4 lg:mb-0">{{ __('Overview') }}</h2>
                        <div class="w-full lg:w-auto">
                            <ul class="flex flex-wrap justify-center lg:justify-end space-x-2 text-sm">
                                @php
                                    $dateRanges = [
                                        'today' => ['from' => now()->format('Y-m-d'), 'to' => now()->format('Y-m-d'), 'label' => __('Today')],
                                        'last7days' => ['from' => now()->subDays(6)->format('Y-m-d'), 'to' => now()->format('Y-m-d'), 'label' => __('Last :days days', ['days' => 7])],
                                        'last30days' => ['from' => now()->subDays(29)->format('Y-m-d'), 'to' => now()->format('Y-m-d'), 'label' => __('Last :days days', ['days' => 30])],
                                        'total' => ['from' => Auth::user()->created_at->format('Y-m-d'), 'to' => now()->format('Y-m-d'), 'label' => __('Total')]
                                    ];
                                @endphp
                                @foreach($dateRanges as $key => $dateRange)
                                    <li>
                                        <a href="{{ route('analytics', ['from' => $dateRange['from'], 'to' => $dateRange['to']]) }}" 
                                        class="inline-block py-2 px-4 rounded-full transition duration-200 ease-in-out
                                                {{ $range['from'] == $dateRange['from'] && $range['to'] == $dateRange['to'] 
                                                    ? 'bg-blue-500 text-white hover:bg-blue-600' 
                                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                            {{ $dateRange['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="bg-white rounded-t shadow-sm my-3 overflow-hidden">
                        <div class="px-3">
                            <div class="flex flex-col md:flex-row">
                                <!-- Title -->
                                <div class="hidden md:flex items-center border-b md:border-b-0 md:border-r">
                                    <div class="px-2 py-4 flex">
                                        <div class="flex relative text-blue-500 w-10 h-10 items-center justify-center flex-shrink-0">
                                            <div class="absolute bg-blue-500 opacity-10 inset-0 rounded-xl"></div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 block" viewBox="0 0 18 18"><path d="M16,0H2A2,2,0,0,0,0,2V16a2,2,0,0,0,2,2H16a2,2,0,0,0,2-2V2A2,2,0,0,0,16,0Zm0,16H2V2H16ZM4,7H6v7H4ZM8,4h2V14H8Zm4,6h2v4H12Z"/></svg>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-grow">
                                    <div class="grid md:grid-cols-2 gap-4">
                                        <!-- Visitors -->
                                        <div class="border-b md:border-b-0 md:border-r">
                                            <div class="p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <div class="flex items-center mb-2">
                                                            <div class="w-4 h-4 bg-blue-500 rounded-full mr-2"></div>
                                                            <h3 class="font-bold text-lg">{{ __('Visitors') }}</h3>
                                                            <x-icon name="heroicon-o-information-circle" class="w-4 h-4 ml-2 text-gray-400" title="{{ __('A visitor represents a page load of your website through direct access, or through a referrer.') }}" />
                                                        </div>
                                                        @include('analytics::stats.growth', ['growthCurrent' => $visitors, 'growthPrevious' => $visitorsOld])
                                                    </div>
                                                    <div class="text-3xl font-bold">
                                                        {{ number_format($visitors, 0, __('.'), __(',')) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Pageviews -->
                                        <div class="border-b md:border-b-0 md:border-r">
                                            <div class="p-4">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <div class="flex items-center mb-2">
                                                            <div class="w-4 h-4 bg-red-500 rounded-full mr-2"></div>
                                                            <h3 class="font-bold text-lg">{{ __('Pageviews') }}</h3>
                                                            <x-icon name="heroicon-o-information-circle" class="w-4 h-4 ml-2 text-gray-400" title="{{ __('A pageview represents a page load of your website.') }}" />
                                                        </div>
                                                        @include('analytics::stats.growth', ['growthCurrent' => $pageviews, 'growthPrevious' => $pageviewsOld])
                                                    </div>
                                                    <div class="text-3xl font-bold">
                                                        {{ number_format($pageviews, 0, __('.'), __(',')) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4 class="mb-0">{{ __('Activity') }}</h4>

                <div class="mt-3">
                    <div class="bg-white rounded shadow-sm">
                        <div class="px-4 py-3 border-b">
                            <div class="flex justify-between items-center">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-xl font-semibold">{{ __('Websites') }}</h2>
                                    <div>
                                        <form method="GET" action="{{ route('analytics') }}" class="flex items-center space-x-2">
                                            <input name="from" type="hidden" value="{{ $range['from'] }}">
                                            <input name="to" type="hidden" value="{{ $range['to'] }}">

                                            <div class="relative">
                                                <input class="form-input pl-8 pr-4 py-2 text-sm rounded-md" name="search" placeholder="{{ __('Search') }}" value="{{ app('request')->input('search') }}">
                                                <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                                    <x-icon name="heroicon-o-magnifying-glass" class="h-4 w-4 text-gray-400 mr-1" />
                                                </div>
                                            </div>
                                            
                                            <div class="relative" x-data="{ open: false }">
                                                <button type="button" @click="open = !open" class="btn btn-outline-primary flex items-center text-sm" title="{{ __('Filters') }}">
                                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                                                    </svg>
                                                    {{ __('Filters') }}
                                                </button>
                                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                                                    <div class="py-1" role="none">
                                                        <div class="px-4 py-2">
                                                            <label for="i-search-by" class="block text-sm font-medium text-gray-700">{{ __('Search by') }}</label>
                                                            <select name="search_by" id="i-search-by" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                                @foreach(['domain' => __('Domain')] as $key => $value)
                                                                    <option value="{{ $key }}" @if(request()->input('search_by') == $key || !request()->input('search_by') && $key == 'name') selected @endif>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="px-4 py-2">
                                                            <label for="i-sort-by" class="block text-sm font-medium text-gray-700">{{ __('Sort by') }}</label>
                                                            <select name="sort_by" id="i-sort-by" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                                @foreach(['id' => __('Date created'), 'domain' => __('Domain')] as $key => $value)
                                                                    <option value="{{ $key }}" @if(request()->input('sort_by') == $key) selected @endif>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="px-4 py-2">
                                                            <label for="i-sort" class="block text-sm font-medium text-gray-700">{{ __('Sort') }}</label>
                                                            <select name="sort" id="i-sort" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                                @foreach(['desc' => __('Descending'), 'asc' => __('Ascending')] as $key => $value)
                                                                    <option value="{{ $key }}" @if(request()->input('sort') == $key) selected @endif>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="px-4 py-2">
                                                            <label for="i-per-page" class="block text-sm font-medium text-gray-700">{{ __('Results per page') }}</label>
                                                            <select name="per_page" id="i-per-page" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                                                @foreach([10, 25, 50, 100] as $value)
                                                                    <option value="{{ $value }}" @if(request()->input('per_page') == $value || request()->input('per_page') == null && $value == config('settings.paginate')) selected @endif>{{ $value }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="py-1" role="none">
                                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-indigo-700 hover:bg-indigo-100 hover:text-indigo-900" role="menuitem" tabindex="-1">{{ __('Apply Filters') }}</button>
                                                        @if(request()->input('per_page'))
                                                            <a href="{{ route('dashboard') }}" class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100 hover:text-red-900" role="menuitem" tabindex="-1">{{ __('Reset Filters') }}</a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            @include('analytics::shared.message')

                            @if(count($websites) == 0)
                                <p class="text-gray-500 text-center py-4">{{ __('No data') }}.</p>
                            @else
                                <div class="space-y-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 text-sm font-medium text-gray-500">
                                            <div class="truncate">{{ __('Domain') }}</div>
                                            <div class="truncate">{{ __('Visitors') }}</div>
                                            <div class="truncate">{{ __('Pageviews') }}</div>
                                        </div>
                                    </div>

                                    @foreach($websites as $website)
                                        <div class="bg-white rounded-lg shadow p-4">
                                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
                                                <div class="flex items-center truncate">
                                                    <img src="https://icons.duckduckgo.com/ip3/{{ $website->domain }}.ico" rel="noreferrer" class="w-5 h-5 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                                    <div class="truncate" dir="ltr">
                                                        <a href="{{ route('stats.overview', ['id' => $website->domain, 'from' => $range['from'], 'to' => $range['to']]) }}" class="text-blue-600 hover:text-blue-800">{{ $website->domain }}</a>
                                                    </div>
                                                </div>

                                                <div class="flex items-center">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 w-3 h-3 bg-blue-500 rounded-full {{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}"></div>
                                                        <span class="font-medium">{{ number_format($website->visitors->sum('count') ?? 0, 0, __('.'), __(',')) }}</span>
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 w-3 h-3 bg-red-500 rounded-full {{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}"></div>
                                                        <span class="font-medium">{{ number_format($website->pageviews->sum('count') ?? 0, 0, __('.'), __(',')) }}</span>
                                                    </div>
                                                    <div>
                                                        @include('websites.partials.menu')
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="flex items-center justify-between mt-6">
                                        <div class="text-sm text-gray-600">
                                            {{ __('Showing :from-:to of :total', ['from' => $websites->firstItem(), 'to' => $websites->lastItem(), 'total' => $websites->total()]) }}
                                        </div>
                                        <div>
                                            {{ $websites->onEachSide(1)->links() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('analytics::shared.sidebars.user')
</x-app>


