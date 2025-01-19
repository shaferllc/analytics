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
            ],
            [
                'url' => route('websites.analytics.countries', ['website' => $site->id]),
                'label' => __('Countries'),
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Countries')"
            :description="__('Countries where your website traffic originates from.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-flag'"
            :totalText="__('Total Countries')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('Country')]"
            :to="$to"
            :search="$search"
        />

        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: 'compact' }" class="space-y-4">
                    <x-analytics::view-switcher :data="$data" color="cyan" />

                    <x-analytics::view view="list" color="cyan" class="bg-gradient-to-br from-cyan-900 to-cyan-950 rounded-xl shadow-lg border border-cyan-800 p-6 backdrop-blur-xl">
                        <div class="flex flex-col space-y-6">
                            @foreach($data as $country)
                                <div class="flex items-center space-x-4 group hover:bg-cyan-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-cyan-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-cyan-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <x-tooltip :text="__('Country')" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-cyan-200 to-cyan-100 bg-clip-text text-transparent">
                                                                    <x-icon name="heroicon-o-flag" class="w-4 h-4 inline mr-1 text-cyan-300" />
                                                                    {{ $country->value ?: __('Unknown') }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <x-tooltip :text="__('Total pageviews from this country')" class="group-hover:opacity-100">
                                                            <span class="hover:text-cyan-200 transition-colors">
                                                                <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1 text-cyan-300" />
                                                                {{ number_format($country->count, 0, __('.'), __(',')) }} {{ __('pageviews') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-cyan-700/30">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($country->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-cyan-400 to-cyan-600 transition-all duration-300 hover:from-cyan-300 hover:to-cyan-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-cyan-300">
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('First seen')" class="group-hover:opacity-100">
                                                            <span class="hover:text-cyan-200 transition-colors">
                                                                <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1 text-cyan-300" />
                                                                {{ $country->created_at->diffForHumans() }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('Last seen')" class="group-hover:opacity-100">
                                                            <span class="hover:text-cyan-200 transition-colors">
                                                                <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1 text-cyan-300" />
                                                                {{ $country->updated_at->diffForHumans() }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <x-tooltip :text="__('Percentage of total pageviews')" class="group-hover:opacity-100">
                                                        <span class="hover:text-cyan-200 transition-colors">
                                                            <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-cyan-300" />
                                                            {{ $aggregates['total_count'] > 0 ? number_format(($country->count / $aggregates['total_count']) * 100, 1) : 0 }}%
                                                        </span>
                                                    </x-tooltip>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-analytics::view>

                    <x-analytics::view view="cards" color="cyan" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($data as $country)
                            <div class="bg-gradient-to-br from-cyan-900 to-cyan-950 rounded-xl shadow-lg border border-cyan-800 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-gradient-to-br from-cyan-700 to-cyan-900 p-2 rounded-full transform group-hover:rotate-12 transition-transform duration-300">
                                            <x-icon name="heroicon-o-flag" class="w-5 h-5 text-cyan-200" />
                                        </div>
                                        <div class="text-sm font-medium text-cyan-100">
                                            {{ $country->value ?: __('Unknown') }}
                                        </div>
                                    </div>
                                    <x-tooltip text="{{ __('Total pageviews') }}">
                                        <div class="text-2xl font-bold text-cyan-100">
                                            <x-icon name="heroicon-o-eye" class="w-5 h-5 inline mr-1" />
                                            {{ number_format($country->count, 0, __('.'), __(',')) }}
                                        </div>
                                    </x-tooltip>
                                </div>

                                <div class="space-y-3">
                                    <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-cyan-700/30">
                                        <div style="width: {{ ($country->count / $aggregates['total_count']) * 100 }}%"
                                            class="shadow-lg bg-gradient-to-r from-cyan-400 to-cyan-600">
                                        </div>
                                    </div>

                                    <div class="flex justify-between text-xs text-cyan-300">
                                        <div class="flex space-x-4">
                                            <x-tooltip :text="__('First seen')" class="group-hover:opacity-100">
                                                <span class="hover:text-cyan-200 transition-colors">
                                                    <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1 text-cyan-300" />
                                                    {{ $country->created_at->diffForHumans() }}
                                                </span>
                                            </x-tooltip>
                                            <x-tooltip :text="__('Last seen')" class="group-hover:opacity-100">
                                                <span class="hover:text-cyan-200 transition-colors">
                                                    <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1 text-cyan-300" />
                                                    {{ $country->updated_at->diffForHumans() }}
                                                </span>
                                            </x-tooltip>
                                        </div>
                                        <x-tooltip :text="__('Percentage of total pageviews')" class="group-hover:opacity-100">
                                            <span class="hover:text-cyan-200 transition-colors">
                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-cyan-300" />
                                                {{ number_format(($country->count / $aggregates['total_count']) * 100, 1) }}%
                                            </span>
                                        </x-tooltip>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-analytics::view>

                    <x-analytics::view view="compact" color="cyan" class="overflow-hidden rounded-xl border border-cyan-800">
                        <table class="min-w-full divide-y divide-cyan-800">
                            <thead class="bg-cyan-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-cyan-300 uppercase tracking-wider">Country</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-cyan-300 uppercase tracking-wider">Pageviews</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-cyan-300 uppercase tracking-wider">Percentage</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-cyan-300 uppercase tracking-wider">Graph</th>
                                </tr>
                            </thead>
                            <tbody class="bg-cyan-950 divide-y divide-cyan-800">
                                @foreach($data as $country)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <x-icon name="heroicon-o-flag" class="w-4 h-4 text-cyan-300 mr-2" />
                                                <div class="text-sm font-medium text-cyan-100 max-w-xs" x-data="{ showFull: false }" @mouseenter="showFull = true" @mouseleave="showFull = false">
                                                    <span x-show="showFull" class="relative inline-block bg-cyan-900 rounded-lg p-2">{{ $country->value ?: __('Unknown') }}</span>
                                                    <span x-show="!showFull" x-cloak class="relative inline-block bg-cyan-900 rounded-lg p-2">{{ Str::limit($country->value, 90) ?: __('Unknown') }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-cyan-100">
                                            <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1 text-cyan-300" />
                                            {{ number_format($country->count, 0, __('.'), __(',')) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-cyan-100">
                                            <x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1 text-cyan-300" />
                                            {{ number_format(($country->count / $aggregates['total_count']) * 100, 1) }}%
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-32 h-2 text-xs flex rounded-full ml-auto">
                                                <div style="width: {{ ($country->count / $aggregates['total_count']) * 100 }}%"
                                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-cyan-400 to-cyan-600">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>


                </div>

                <div class="mt-6">
                    <x-analytics::pagination :data="$data" />
                </div>
            @endif
        </div>
    </div>
</x-website>
