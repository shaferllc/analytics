<x-website :website="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('websites.analytics.overview', ['website' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('websites.analytics.geographic', ['website' => $site->id]),
                'label' => __('Geographic'),
            ],
            [
                'url' => route('websites.analytics.cities', ['website' => $site->id]),
                'label' => __('Cities'),
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Cities')"
            :description="__('Cities where your website is being accessed from.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-building-office-2'"
            :totalText="__('Total Cities')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('City')]"
            :to="$to"
            :search="$search"
        />

        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: '{{ $display }}' }" class="space-y-4">
                    <x-analytics::view-switcher :data="$data" color="purple" />

                    <x-analytics::view view="list" color="purple" class="bg-gradient-to-br from-purple-900 to-purple-950 rounded-xl shadow-lg border border-purple-800 p-6 backdrop-blur-xl">
                        <div class="flex flex-col space-y-6">
                            @foreach($data as $city)
                                <div class="flex items-center space-x-4 group hover:bg-purple-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-purple-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-purple-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <x-tooltip :text="$city->value ?: __('Unknown')" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-purple-200 to-purple-100 bg-clip-text text-transparent">
                                                                    <x-icon name="heroicon-o-building-office-2" class="w-4 h-4 inline mr-1" />
                                                                    {{ $city->value ?: __('Unknown') }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                        <div class="flex items-center space-x-2 mt-1">
                                                            <x-tooltip :text="__('First seen')" class="group-hover:opacity-100">
                                                                <span class="text-xs text-purple-400 hover:text-purple-300 transition-colors">
                                                                    <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1" />
                                                                    {{ $city->created_at->diffForHumans() }}
                                                                </span>
                                                            </x-tooltip>
                                                            <x-tooltip :text="__('Last seen')" class="group-hover:opacity-100">
                                                                <span class="text-xs text-purple-400 hover:text-purple-300 transition-colors">
                                                                    <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1" />
                                                                    {{ $city->updated_at->diffForHumans() }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-purple-700/30">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($city->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-purple-400 to-purple-600 transition-all duration-300 hover:from-purple-300 hover:to-purple-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-purple-300">
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('Total pageviews from this city')" class="group-hover:opacity-100">
                                                            <span class="hover:text-purple-200 transition-colors">
                                                                <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1" />
                                                                {{ number_format($city->count, 0, __('.'), __(',')) }} {{ __('pageviews') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('Percentage of total pageviews')" class="group-hover:opacity-100">
                                                            <span class="hover:text-purple-200 transition-colors">
                                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                                {{ $aggregates['total_count'] > 0 ? number_format(($city->count / $aggregates['total_count']) * 100, 1) : 0 }}% {{ __('of pageviews') }}
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

                    <x-analytics::view view="compact" color="purple" class="overflow-hidden rounded-xl border border-purple-800">
                        <table class="min-w-full divide-y divide-purple-800">
                            <thead class="bg-purple-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-purple-300 uppercase tracking-wider">{{ __('City') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-purple-300 uppercase tracking-wider">{{ __('Pageviews') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-purple-300 uppercase tracking-wider">{{ __('Percentage') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-purple-300 uppercase tracking-wider">{{ __('Graph') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-purple-950 divide-y divide-purple-800">
                                @foreach($data as $city)
                                    <tr class="hover:bg-purple-900/50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <x-tooltip :text="$city->value ?: __('Unknown')">
                                                    <div class="text-sm font-medium text-purple-100 max-w-xs truncate">
                                                        <x-icon name="heroicon-o-building-office-2" class="w-4 h-4 inline mr-2 text-purple-400" />
                                                        {{ Str::limit($city->value, 90) ?: __('Unknown') }}
                                                    </div>
                                                </x-tooltip>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-purple-100">
                                            <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1 text-purple-400" />
                                            {{ number_format($city->count, 0, __('.'), __(',')) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-purple-100">
                                            <x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1 text-purple-400" />
                                            {{ number_format(($city->count / $aggregates['total_count']) * 100, 1) }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-tooltip :text="number_format($city->count) . ' ' . __('pageviews')">
                                                <div class="w-32 h-2 text-xs flex rounded-full ml-auto bg-purple-700/30">
                                                    <div style="width: {{ ($city->count / $aggregates['total_count']) * 100 }}%"
                                                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-purple-400 to-purple-600 rounded-full">
                                                    </div>
                                                </div>
                                            </x-tooltip>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>

                    <x-analytics::view view="cards" color="purple" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($data as $city)
                            <div class="group bg-gradient-to-br from-purple-900 to-purple-950 rounded-xl shadow-lg border border-purple-800 p-6 hover:shadow-xl transition-all duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-purple-800/20 blur-xl rounded-full group-hover:bg-purple-700/30 transition-colors duration-300"></div>
                                            <x-tooltip :text="__('City') . ': ' . $city->value">
                                                <div class="relative bg-gradient-to-br from-purple-700 to-purple-900 p-3 rounded-full group-hover:from-purple-600 group-hover:to-purple-800 transition-colors duration-300">
                                                    <x-icon name="heroicon-o-building-office-2" class="w-6 h-6 text-purple-200 group-hover:animate-spin-slow" />
                                                </div>
                                            </x-tooltip>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <x-tooltip :text="number_format($city->count) . ' ' . __('pageviews from') . ' ' . $city->value . ' - ' . number_format(($city->count / $aggregates['total_count']) * 100, 1) . '% ' . __('of total traffic')">
                                            <span class="text-2xl font-bold text-purple-100 group-hover:text-white transition-colors duration-300">
                                                <x-icon name="heroicon-o-eye" class="w-6 h-6 inline mr-2" />
                                                {{ number_format($city->count, 0, __('.'), __(',')) }}
                                            </span>
                                        </x-tooltip>
                                        <p class="text-purple-300 text-sm group-hover:text-purple-200 transition-colors duration-300">{{ __('Pageviews') }}</p>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-purple-100 mb-1 group-hover:text-white transition-colors duration-300">
                                        {{ $city->value ?: __('Unknown') }}
                                    </h3>
                                    <x-tooltip :text="number_format($city->count) . ' ' . __('pageviews')">
                                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-700/50 text-purple-200 group-hover:bg-purple-600/50 group-hover:text-white transition-all duration-300">
                                            <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                            {{ number_format(($city->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of total') }}
                                        </div>
                                    </x-tooltip>
                                </div>

                                <div class="relative pt-1">
                                    <x-tooltip :text="number_format($city->count) . ' ' . __('pageviews') . ' - ' . number_format(($city->count / $aggregates['total_count']) * 100, 1) . '% ' . __('of total traffic')">
                                        <div class="overflow-hidden h-2 text-xs flex rounded-full bg-purple-700/30">
                                            <div style="width: {{ ($city->count / $aggregates['total_count']) * 100 }}%"
                                                class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-purple-400 to-purple-600 group-hover:from-purple-300 group-hover:to-purple-500 transition-colors duration-300 animate-pulse">
                                            </div>
                                        </div>
                                    </x-tooltip>
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
