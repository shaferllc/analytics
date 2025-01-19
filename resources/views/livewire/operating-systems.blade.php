<x-website :website="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('websites.analytics.overview', ['website' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('websites.analytics.technology', ['website' => $site->id]),
                'label' => __('Technology'),
                'icon' => 'heroicon-o-cpu-chip',
            ],
            [
                'url' => route('websites.analytics.operating-systems', ['website' => $site->id]),
                'label' => __('Operating Systems'),
                'icon' => 'heroicon-o-computer-desktop',
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Operating Systems')"
            :description="__('Operating systems used to access your website.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-computer-desktop'"
            :totalText="__('Total Operating Systems')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('Operating System')]"
            :to="$to"
            :search="$search"
        />

        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: '{{ $display }}', hoveredItem: null, animate: true }"
                     x-init="setTimeout(() => animate = false, 1000)"
                     class="space-y-4">
                    <x-analytics::view-switcher :data="$data" color="indigo" />

                    <x-analytics::view view="list" color="indigo" class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6 backdrop-blur-xl">
                        <div class="flex flex-col space-y-6">
                            @foreach($data as $operatingSystem)
                                <div class="flex items-center space-x-4 group hover:bg-indigo-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-indigo-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-indigo-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <x-tooltip :text="__('Operating System')" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-indigo-200 to-indigo-100 bg-clip-text text-transparent">
                                                                    <img src="{{ asset('/vendor/analytics/icons/os/'.formatOperatingSystem($operatingSystem->value)) }}.svg" class="w-4 h-4 inline mr-1 text-indigo-200">
                                                                    {{ $operatingSystem->value ? $operatingSystem->value : __('Unknown') }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <x-tooltip :text="__('Total pageviews from this operating system')" class="group-hover:opacity-100">
                                                            <span class="text-2xl font-bold text-indigo-100 group-hover:text-indigo-50 transition-colors">
                                                                {{ number_format($operatingSystem->count, 0, __('.'), __(',')) }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-indigo-700/30">
                                                    <div style="width: {{ ($operatingSystem->count / $aggregates['total_count']) * 100 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all duration-300 hover:from-indigo-300 hover:to-indigo-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-indigo-300">
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('First seen')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ $operatingSystem->created_at->diffForHumans() }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('Last seen')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ $operatingSystem->updated_at->diffForHumans() }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('Percentage of total pageviews')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ number_format(($operatingSystem->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of total') }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('Average daily pageviews')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-chart-bar" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ round($operatingSystem->count / max(1, (strtotime($operatingSystem->updated_at) - strtotime($operatingSystem->created_at)) / 86400), 1) }} {{ __('views/day') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-analytics::view>

                    <x-analytics::view view="compact" color="indigo" class="overflow-hidden rounded-xl border border-indigo-800 backdrop-blur-xl">
                        <table class="min-w-full divide-y divide-indigo-800">
                            <thead class="bg-indigo-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-300 uppercase tracking-wider">Operating System</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">Pageviews</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">Percentage</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">Daily Avg</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">Graph</th>
                                </tr>
                            </thead>
                            <tbody class="bg-indigo-950 divide-y divide-indigo-800">
                                @foreach($data as $operatingSystem)
                                    <tr class="group hover:bg-indigo-900/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                <div class="bg-gradient-to-br from-indigo-700 to-indigo-900 p-2 rounded-full transform group-hover:rotate-12 transition-transform duration-300">
                                                    <img src="{{ asset('/vendor/analytics/icons/os/'.formatOperatingSystem($operatingSystem->value)) }}.svg" class="w-5 h-5 text-indigo-200">
                                                </div>
                                                <div class="text-sm font-medium text-indigo-100 group-hover:text-indigo-50 transition-colors">
                                                    {{ $operatingSystem->value ? $operatingSystem->value : __('Unknown') }}
                                                </div>
                                            </div>
                                            <div class="text-xs text-indigo-400/70 mt-1 group-hover:text-indigo-300/70 transition-colors">
                                                {{ __('First seen') }} {{ $operatingSystem->created_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm text-indigo-100 group-hover:text-indigo-50 transition-colors font-medium">
                                                {{ number_format($operatingSystem->count, 0, __('.'), __(',')) }}
                                            </div>
                                            <div class="text-xs text-indigo-400/70 group-hover:text-indigo-300/70 transition-colors">
                                                {{ __('pageviews') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm text-indigo-100 group-hover:text-indigo-50 transition-colors font-medium">
                                                {{ number_format(($operatingSystem->count / $aggregates['total_count']) * 100, 1) }}%
                                            </div>
                                            <div class="text-xs text-indigo-400/70 group-hover:text-indigo-300/70 transition-colors">
                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                {{ __('of total') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm text-indigo-100 group-hover:text-indigo-50 transition-colors font-medium">
                                                {{ round($operatingSystem->count / max(1, (strtotime($operatingSystem->updated_at) - strtotime($operatingSystem->created_at)) / 86400), 1) }}
                                            </div>
                                            <div class="text-xs text-indigo-400/70 group-hover:text-indigo-300/70 transition-colors">
                                                {{ __('views/day') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-32 h-2 text-xs flex rounded-full bg-indigo-700/30 ml-auto group-hover:bg-indigo-600/30 transition-colors">
                                                <div style="width: {{ ($operatingSystem->count / $aggregates['total_count']) * 100 }}%"
                                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-400 to-indigo-600 group-hover:from-indigo-500 group-hover:to-indigo-700 transition-colors animate-pulse">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>

                    <x-analytics::view view="cards" color="indigo" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($data as $operatingSystem)
                            <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-lg shadow p-4 flex flex-col justify-between group hover:from-indigo-800 hover:to-indigo-900 transition-all duration-300 hover:scale-105 backdrop-blur-xl">
                                <div>
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="p-2 bg-indigo-800/50 rounded-lg group-hover:bg-indigo-700/50 transition-colors transform group-hover:rotate-12 duration-300">
                                            <img src="{{ asset('/vendor/analytics/icons/os/'.formatOperatingSystem($operatingSystem->value)) }}.svg" class="w-5 h-5 text-indigo-200">
                                        </div>
                                        <div class="text-xs text-indigo-400/70 group-hover:text-indigo-300/70 transition-colors">
                                            {{ $operatingSystem->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-medium text-indigo-200 mb-2 group-hover:text-indigo-100 transition-colors">
                                        {{ $operatingSystem->value ? $operatingSystem->value : __('Unknown') }}
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-2xl font-bold text-indigo-100 group-hover:text-indigo-50 transition-colors">{{ number_format($operatingSystem->count) }}</div>
                                    <div class="text-xs text-indigo-300 group-hover:text-indigo-200 transition-colors">{{ __('pageviews') }}</div>
                                    <div class="text-xs text-indigo-400/70 group-hover:text-indigo-300/70 transition-colors">
                                        <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                        {{ number_format(($operatingSystem->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of total') }}
                                    </div>
                                    <div class="mt-3 h-1 bg-indigo-700/30 rounded-full overflow-hidden group-hover:bg-indigo-600/30 transition-colors">
                                        <div class="h-full bg-gradient-to-r from-indigo-400 to-indigo-600 group-hover:from-indigo-500 group-hover:to-indigo-700 transition-colors animate-pulse"
                                            style="width: {{ ($operatingSystem->count / $aggregates['total_count']) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-analytics::view>

                </div>
                <div class="mt-6">
                    <x-analytics::pagination :data="$data" />
                </div>
            @endif
        </div>
    </div>
</x-website>
