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
                'url' => route('websites.analytics.timezones', ['website' => $site->id]),
                'label' => __('Timezones'),
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Timezones')"
            :description="__('Timezones used to access your website.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-clock'"
            :totalText="__('Total Timezones')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('Timezone')]"
            :to="$to"
            :search="$search"
        />

        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: 'compact' }" class="space-y-4">
                    <x-analytics::view-switcher :data="$data" color="pink" />

                    <x-analytics::view view="list" color="pink" class="bg-gradient-to-br from-pink-900 to-pink-950 rounded-xl shadow-lg border border-pink-800 p-6 backdrop-blur-xl">
                        <div class="flex flex-col space-y-6">
                            @foreach($data as $timezone)
                                <div class="flex items-center space-x-4 group hover:bg-pink-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-pink-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-pink-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2">
                                                        <x-tooltip :text="__('Timezone: :timezone', ['timezone' => $timezone->value])" class="group-hover:opacity-100">
                                                            <span class="font-medium bg-gradient-to-r from-pink-200 to-pink-100 bg-clip-text text-transparent">
                                                                <x-icon name="heroicon-o-clock" class="w-4 h-4 inline mr-1 text-pink-300" />
                                                                {{ $timezone->value ?: __('Unknown') }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('First seen :time', ['time' => $timezone->created_at->diffForHumans()])" class="group-hover:opacity-100">
                                                            <span class="text-pink-300 hover:text-pink-200 transition-colors">
                                                                <x-icon name="heroicon-o-calendar" class="w-4 h-4 inline" />
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <x-tooltip :text="__('Total pageviews from this timezone')" class="group-hover:opacity-100">
                                                            <span class="text-pink-300 hover:text-pink-200 transition-colors">
                                                                <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1 text-pink-300" />
                                                                {{ number_format($timezone->count, 0, __('.'), __(',')) }} {{ __('pageviews') }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('Last updated :time', ['time' => $timezone->updated_at->diffForHumans()])" class="group-hover:opacity-100">
                                                            <span class="text-pink-300 hover:text-pink-200 transition-colors">
                                                                <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 inline" />
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-pink-700/30">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($timezone->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-pink-400 to-pink-600 transition-all duration-300 hover:from-pink-300 hover:to-pink-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-pink-300">
                                                    <x-tooltip :text="__('Percentage of total pageviews')" class="group-hover:opacity-100">
                                                        <span class="hover:text-pink-200 transition-colors">
                                                            <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-pink-300" />
                                                            {{ $aggregates['total_count'] > 0 ? number_format(($timezone->count / $aggregates['total_count']) * 100, 1) : 0 }}% {{ __('of total') }}
                                                        </span>
                                                    </x-tooltip>
                                                    <x-tooltip :text="__('Trend')" class="group-hover:opacity-100">
                                                        <span class="hover:text-pink-200 transition-colors">
                                                            <x-icon name="heroicon-o-chart-bar" class="w-3 h-3 inline mr-1 text-pink-300" />
                                                            {{ __('View trend') }}
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

                    <x-analytics::view view="compact" color="pink" class="overflow-hidden rounded-xl border border-pink-800 shadow-lg">
                        <table class="min-w-full divide-y divide-pink-800">
                            <thead class="bg-gradient-to-br from-pink-900 to-pink-950">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-pink-300 uppercase tracking-wider">
                                        <x-tooltip text="{{ __('Timezone') }}">
                                            <span><x-icon name="heroicon-o-globe-alt" class="w-4 h-4 inline mr-1" />{{ __('Timezone') }}</span>
                                        </x-tooltip>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-pink-300 uppercase tracking-wider">
                                        <x-tooltip text="{{ __('Total pageviews') }}">
                                            <span><x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />{{ __('Pageviews') }}</span>
                                        </x-tooltip>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-pink-300 uppercase tracking-wider">
                                        <x-tooltip text="{{ __('Percentage of total pageviews') }}">
                                            <span><x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1" />{{ __('Percentage') }}</span>
                                        </x-tooltip>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-pink-300 uppercase tracking-wider">
                                        <x-tooltip text="{{ __('Visual representation') }}">
                                            <span><x-icon name="heroicon-o-chart-bar" class="w-4 h-4 inline mr-1" />{{ __('Graph') }}</span>
                                        </x-tooltip>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-pink-950 divide-y divide-pink-800">
                                @foreach($data as $timezone)
                                    <tr class="hover:bg-pink-900/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-3">
                                                <div class="bg-gradient-to-br from-pink-700 to-pink-900 p-2 rounded-full">
                                                    <x-icon name="heroicon-o-clock" class="w-5 h-5 text-pink-200" />
                                                </div>
                                                <div class="text-sm font-medium text-pink-100">
                                                    {{ $timezone->value ? $timezone->value : __('Unknown') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-pink-100">
                                            <x-tooltip text="{{ __('Total pageviews') }}">
                                                <span>
                                                    <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />
                                                    {{ number_format($timezone->count, 0, __('.'), __(',')) }}
                                                </span>
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-pink-100">
                                            <x-tooltip text="{{ __('Percentage of total pageviews') }}">
                                                <span>
                                                    <x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1" />
                                                    {{ $aggregates['total_count'] > 0 ? number_format(($timezone->count / $aggregates['total_count']) * 100, 1) : 0 }}%
                                                </span>
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-tooltip text="{{ number_format($timezone->count, 0, __('.'), __(',')) }} {{ __('pageviews') }} ({{ $aggregates['total_count'] > 0 ? number_format(($timezone->count / $aggregates['total_count']) * 100, 1) : 0 }}%) {{ __('from') }} {{ $timezone->value }}">
                                                <div class="w-32 overflow-hidden h-2 text-xs flex rounded-full bg-pink-700/30 ml-auto">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($timezone->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-pink-400 to-pink-600 animate-pulse">
                                                    </div>
                                                </div>
                                            </x-tooltip>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>

                    <x-analytics::view view="cards" color="pink" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($data as $timezone)
                            <div class="bg-gradient-to-br from-pink-900 to-pink-950 rounded-xl shadow-lg border border-pink-800 p-6 backdrop-blur-xl hover:shadow-pink-500/20 transition-all duration-300 hover:scale-[1.02] group">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-gradient-to-br from-pink-700 to-pink-900 p-2 rounded-full transform group-hover:rotate-12 transition-transform duration-300">
                                            <x-icon name="heroicon-o-clock" class="w-5 h-5 text-pink-200" />
                                        </div>
                                        <div class="text-sm font-medium text-pink-100">
                                            {{ $timezone->value ? $timezone->value : __('Unknown') }}
                                        </div>
                                    </div>
                                    <x-tooltip text="{{ __('Total pageviews') }}">
                                        <div class="text-2xl font-bold text-pink-100">
                                            <x-icon name="heroicon-o-eye" class="w-5 h-5 inline mr-1" />
                                            {{ number_format($timezone->count, 0, __('.'), __(',')) }}
                                        </div>
                                    </x-tooltip>
                                </div>

                                <div class="space-y-3">
                                    <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-pink-700/30">
                                        <div style="width: {{ $aggregates['total_count'] > 0 ? ($timezone->count / $aggregates['total_count']) * 100 : 0 }}%"
                                            class="shadow-lg bg-gradient-to-r from-pink-400 to-pink-600 transition-all duration-300 group-hover:from-pink-300 group-hover:to-pink-500">
                                        </div>
                                    </div>

                                    <div class="flex justify-between text-xs text-pink-300">
                                        <div class="flex space-x-4">
                                            <x-tooltip :text="__('First seen')" class="group-hover:opacity-100">
                                                <span class="hover:text-pink-200 transition-colors">
                                                    <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1 text-pink-300" />
                                                    {{ $timezone->created_at->diffForHumans() }}
                                                </span>
                                            </x-tooltip>
                                            <x-tooltip :text="__('Last seen')" class="group-hover:opacity-100">
                                                <span class="hover:text-pink-200 transition-colors">
                                                    <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1 text-pink-300" />
                                                    {{ $timezone->updated_at->diffForHumans() }}
                                                </span>
                                            </x-tooltip>
                                        </div>
                                        <x-tooltip :text="__('Percentage of total pageviews')" class="group-hover:opacity-100">
                                            <span class="hover:text-pink-200 transition-colors">
                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-pink-300" />
                                                {{ $aggregates['total_count'] > 0 ? number_format(($timezone->count / $aggregates['total_count']) * 100, 1) : 0 }}%
                                            </span>
                                        </x-tooltip>
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
