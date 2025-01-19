<x-site :site="$site">
    <div class="min-h-screen flex">
        <div class="w-full p-8">
            <div class="w-full max-w-7xl mx-auto space-y-6">
                <x-analytics::breadcrumbs :breadcrumbs="[
                    [
                        'url' => route('sites.analytics.overview', ['site' => $site->id]),
                        'label' => __('Dashboard'),
                    ],
                    [
                        'url' => route('sites.analytics.acquisitions', ['site' => $site->id]),
                        'label' => __('Acquisitions'),
                        'icon' => 'heroicon-o-chart-pie',
                    ],
                    [
                        'url' => route('sites.analytics.pages', ['site' => $site->id]),
                        'label' => __('Pages'),
                        'icon' => 'heroicon-o-document-text',
                    ]
                ]" />

                @include('analytics::livewire.partials.nav')

                <x-analytics::title
                    :title="__('Pages')"
                    :description="__('Track which pages are most visited on your website.')"
                    :totalPageviews="$total"
                    :icon="'heroicon-o-document-text'"
                    :totalText="__('Total Pages')"
                    :data="$data"
                    :total="$total"
                    :first="$first"
                    :last="$last"
                    :website="$site"
                    :daterange="$daterange"
                    :perPage="$perPage"
                    :sortBy="$sortBy"
                    :sort="$sort"
                    :from="$from"
                    :color="'gray'"
                    :sortWords="['count' => __('Pageviews'), 'value' => __('Page')]"
                    :to="$to"
                    :search="$search"
                />

                <div>
                    @if(count($data) == 0)
                        <x-analytics::no-results />
                    @else
                        <div x-data="{ view: '{{ $display }}' }" class="space-y-6" wire:poll.10s>
                            <div class="flex justify-end">
                                <x-analytics::view-switcher :data="$data" color="gray" />
                            </div>

                            <x-analytics::view view="list" color="gray" class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800">
                                @foreach($data as $page)
                                    <div class="p-6 flex flex-col space-y-4 border-b last:border-b-0 border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-300">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center space-x-3 group">
                                                    <x-icon name="heroicon-o-document-text" class="w-6 h-6 text-gray-500 dark:text-gray-400 transition-all duration-300 group-hover:scale-110" />
                                                    <span class="transition-colors duration-300 group-hover:text-gray-600 dark:group-hover:text-gray-300 break-words break-all">{{ $page->value ?: __('Unknown') }}</span>
                                                </h3>

                                                <div class="flex items-center space-x-3 mt-3">
                                                    <div class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 transition-all duration-300 hover:bg-gray-200 dark:hover:bg-gray-700">
                                                        <x-icon name="heroicon-o-chart-pie" class="w-4 h-4 mr-2 transition-transform duration-300 hover:rotate-180" />
                                                        @if($aggregates['total_count'] > 0)
                                                            {{ number_format(($page->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of total traffic') }}
                                                        @else
                                                            0% {{ __('of total traffic') }}
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('sites.analytics.page', ['site' => $site->id, 'value' => encrypt($page->value)]) }}"
                                                       class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 transition-all duration-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                                                        {{ __('View Details') }}
                                                        <x-icon name="heroicon-o-arrow-right" class="w-4 h-4 ml-2" />
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="text-right">
                                                <div class="flex items-center justify-end space-x-3 group">
                                                    <x-icon name="heroicon-o-eye" class="w-6 h-6 text-gray-500 dark:text-gray-400 transition-transform duration-300 group-hover:scale-110" />
                                                    <span class="text-3xl font-bold text-gray-900 dark:text-white transition-all duration-300 group-hover:text-gray-600 dark:group-hover:text-gray-300">
                                                        {{ number_format($page->count, 0, __('.'), __(',')) }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ __('Total Pageviews') }}</p>
                                            </div>
                                        </div>

                                        <div class="relative pt-2">
                                            <div class="overflow-hidden h-2.5 text-xs flex rounded-full bg-gray-200 dark:bg-gray-700">
                                                <div
                                                    @if($aggregates['total_count'] > 0)
                                                        style="width: {{ ($page->count / $aggregates['total_count']) * 100 }}%"
                                                    @else
                                                        style="width: 0%"
                                                    @endif
                                                    class="shadow-lg flex flex-col text-center whitespace-nowrap text-white justify-center bg-gray-500 dark:bg-gray-400 transition-all duration-700 ease-in-out hover:bg-gray-600 dark:hover:bg-gray-500"
                                                    x-data
                                                    x-init="setTimeout(() => {
                                                        @if($aggregates['total_count'] > 0)
                                                            $el.style.width = '{{ ($page->count / $aggregates['total_count']) * 100 }}%'
                                                        @else
                                                            $el.style.width = '0%'
                                                        @endif
                                                    }, 100)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </x-analytics::view>

                            <x-analytics::view view="compact" color="gray" class="overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-800 shadow-lg">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <div class="flex items-center space-x-2 group">
                                                    <x-icon name="heroicon-o-camera" class="w-4 h-4 transition-transform duration-300 group-hover:rotate-6" />
                                                    <span class="transition-colors duration-300 group-hover:text-gray-600 dark:group-hover:text-gray-300">{{ __('Screenshot') }}</span>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <div class="flex items-center space-x-2 group">
                                                    <x-icon name="heroicon-o-document-text" class="w-4 h-4 transition-transform duration-300 group-hover:rotate-6" />
                                                    <span class="transition-colors duration-300 group-hover:text-gray-600 dark:group-hover:text-gray-300">{{ __('Page') }}</span>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <div class="flex items-center justify-end space-x-2 group">
                                                    <x-icon name="heroicon-o-eye" class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" />
                                                    <span class="transition-colors duration-300 group-hover:text-gray-600 dark:group-hover:text-gray-300">{{ __('Pageviews') }}</span>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <div class="flex items-center justify-end space-x-2 group">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-4 h-4 transition-transform duration-300 group-hover:rotate-180" />
                                                    <span class="transition-colors duration-300 group-hover:text-gray-600 dark:group-hover:text-gray-300">{{ __('Percentage') }}</span>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <div class="flex items-center justify-end space-x-2 group">
                                                    <x-icon name="heroicon-o-chart-bar" class="w-4 h-4 transition-transform duration-300 group-hover:scale-110" />
                                                    <span class="transition-colors duration-300 group-hover:text-gray-600 dark:group-hover:text-gray-300">{{ __('Graph') }}</span>
                                                </div>
                                            </th>
                                            <th scope="col" class="px-6 py-4 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                <span class="sr-only">{{ __('Actions') }}</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($data as $page)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div x-data="{ open: false }">
                                                            <img
                                                                src="{{ $page->screenshot }}"
                                                                class="w-12 h-12 rounded-lg shadow-lg cursor-pointer hover:opacity-75 transition-all duration-200 hover:scale-105"
                                                                @click="open = true"
                                                            />
                                                            <div x-show="open"
                                                                 @click.away="open = false"
                                                                 class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm">
                                                                <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl max-w-4xl max-h-[90vh] overflow-auto">
                                                                    <div class="flex justify-between mb-4 sticky top-0 bg-white dark:bg-gray-800 z-10">
                                                                        <button @click="open = false" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                                                            <x-icon name="heroicon-o-x-circle" class="w-6 h-6"/>
                                                                        </button>
                                                                    </div>
                                                                    <div class="overflow-auto">
                                                                        <img src="{{ $page->screenshot }}" class="max-w-full h-auto rounded-lg"/>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white max-w-xs" x-data="{ showFull: false }" @mouseenter="showFull = true" @mouseleave="showFull = false">
                                                            <span x-show="showFull" class="relative inline-block bg-gray-50 dark:bg-gray-800 rounded-lg p-2.5">{{ $page->value ?: __('Unknown') }}</span>
                                                            <span x-show="!showFull" x-cloak class="relative inline-block bg-gray-50 dark:bg-gray-800 rounded-lg p-2.5">{{ Str::limit($page->value, 90) ?: __('Unknown') }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                    <x-tooltip text="{{ __('Total pageviews for this page') }}">
                                                        <span class="text-gray-900 dark:text-white font-semibold transition-colors duration-300 hover:text-gray-600 dark:hover:text-gray-300">
                                                            {{ number_format($page->count, 0, __('.'), __(',')) }}
                                                        </span>
                                                    </x-tooltip>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                    <x-tooltip text="{{ __('Percentage of total pageviews') }}">
                                                        <span class="text-gray-900 dark:text-white font-semibold transition-colors duration-300 hover:text-gray-600 dark:hover:text-gray-300">
                                                            @if($aggregates['total_count'] > 0)
                                                                {{ number_format(($page->count / $aggregates['total_count']) * 100, 1) }}%
                                                            @else
                                                                0%
                                                            @endif
                                                        </span>
                                                    </x-tooltip>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="w-32 h-2.5 text-xs flex rounded-full ml-auto overflow-hidden bg-gray-200 dark:bg-gray-700">
                                                        <div style="width: 0%"
                                                            x-data
                                                            x-init="setTimeout(() => $el.style.width = '{{ $aggregates['total_count'] > 0 ? ($page->count / $aggregates['total_count']) * 100 : 0 }}%', 100)"
                                                            class="shadow-lg flex flex-col text-center whitespace-nowrap text-white justify-center bg-gray-500 dark:bg-gray-400 transition-all duration-1000 ease-in-out hover:bg-gray-600 dark:hover:bg-gray-500">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                    <a href="{{ route('sites.analytics.page', ['site' => $site->id, 'value' => encrypt($page->value)]) }}"
                                                       class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 transition-all duration-300 hover:bg-gray-300 dark:hover:bg-gray-600">
                                                        <x-icon name="heroicon-o-arrow-right" class="w-4 h-4 mr-2" />
                                                        {{ __('View Details') }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </x-analytics::view>

                            <x-analytics::view view="cards" color="gray" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($data as $page)
                                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-800 p-6 hover:bg-gray-50 dark:hover:bg-gray-800 group relative hover:scale-[1.02]">
                                        <div class="flex flex-col space-y-6">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                <div class="flex items-center space-x-3 mb-3">
                                                    <div class="flex-initial">
                                                        <x-icon name="heroicon-o-document-text" class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors duration-300" />
                                                    </div>
                                                    <div class="flex-1 break-words break-all font-medium">
                                                        {{ $page->value ?: __('Unknown') }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300">
                                                    <span class="text-gray-600 dark:text-gray-400 text-sm flex items-center mb-1">
                                                        <x-icon name="heroicon-o-eye" class="w-4 h-4 mr-2" />
                                                        {{ __('Pageviews') }}
                                                    </span>
                                                    <span class="text-gray-900 dark:text-white text-lg font-bold group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors duration-300">
                                                        {{ number_format($page->count, 0, __('.'), __(',')) }}
                                                    </span>
                                                </div>
                                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300">
                                                    <span class="text-gray-600 dark:text-gray-400 text-sm flex items-center mb-1">
                                                        <x-icon name="heroicon-o-chart-pie" class="w-4 h-4 mr-2" />
                                                        {{ __('Percentage') }}
                                                    </span>
                                                    <span class="text-gray-900 dark:text-white text-lg font-bold group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors duration-300">
                                                        {{ number_format(($page->count / $aggregates['total_count']) * 100, 1) }}%
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="relative pt-1">
                                                <div class="overflow-hidden h-3 text-xs flex rounded-lg bg-gray-200 dark:bg-gray-700">
                                                    <div style="width: 0%"
                                                        x-data
                                                        x-init="setTimeout(() => $el.style.width = '{{ ($page->count / $aggregates['total_count']) * 100 }}%', 300)"
                                                        class="shadow-lg bg-gray-500 dark:bg-gray-400 transition-all duration-1000 ease-in-out hover:bg-gray-600 dark:hover:bg-gray-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <a href="{{ route('sites.analytics.page', ['site' => $site->id, 'value' => encrypt($page->value)]) }}"
                                               class="inline-flex items-center justify-center px-6 py-3 rounded-xl text-sm font-medium bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 transition-all duration-300 hover:bg-gray-300 dark:hover:bg-gray-600 hover:scale-105">
                                                {{ __('View Details') }}
                                                <x-icon name="heroicon-o-arrow-right" class="w-4 h-4 ml-2" />
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </x-analytics::view>
                        </div>

                        <div class="mt-8">
                            <x-analytics::pagination :data="$data" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-site>
