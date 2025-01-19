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
                    'url' => route('websites.analytics.browsers', ['website' => $site->id]),
                    'label' => __('Browsers'),
                    'icon' => 'heroicon-o-rectangle-stack',
                ]
            ]" />

        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Browsers')"
            :description="__('Browsers are the web browsers used to access your website. This includes Chrome, Firefox, Safari and others.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-globe-alt'"
            :totalText="__('Total Browsers')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('Browser')]"
            :to="$to"
            :search="$search"
        />

        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: 'compact', hoveredItem: null, animate: true }"
                     x-init="setTimeout(() => animate = false, 1000)"
                     class="space-y-4">
                    <x-analytics::view-switcher :data="$data" color="emerald" />

                    <x-analytics::view view="list" color="emerald" class="bg-gradient-to-br from-emerald-900 to-emerald-950 rounded-xl shadow-lg border border-emerald-800 p-6 backdrop-blur-xl">
                        <div class="flex flex-col space-y-6">
                            @foreach($data as $browser)
                                <div class="flex items-center space-x-4 group hover:bg-emerald-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-emerald-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-emerald-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <x-tooltip :text="__('Browser Version')" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-emerald-200 to-emerald-100 bg-clip-text text-transparent">
                                                                    <img src="{{ asset('/vendor/analytics/icons/browsers/'.formatBrowser(strtolower($browser->value))) }}.svg" class="w-4 h-4 inline mr-1 text-emerald-300">
                                                                    {{ $browser->value ? $browser->value : __('Unknown') }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <x-tooltip :text="__('Total Pageviews')" class="group-hover:opacity-100">
                                                            <span class="text-2xl font-bold text-emerald-100 group-hover:text-emerald-50 transition-colors">
                                                                {{ number_format($browser->count, 0, __('.'), __(',')) }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-emerald-700/30">
                                                    <div style="width: {{ ($browser->count / $aggregates['total_count']) * 100 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-300 hover:from-emerald-300 hover:to-emerald-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-emerald-300">
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('Total pageviews from this browser')" class="group-hover:opacity-100">
                                                            <span class="hover:text-emerald-200 transition-colors">
                                                                <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1 text-emerald-300" />
                                                                {{ number_format($browser->count, 0, __('.'), __(',')) }} {{ __('pageviews') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('Percentage of total pageviews')" class="group-hover:opacity-100">
                                                            <span class="hover:text-emerald-200 transition-colors">
                                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-emerald-300" />
                                                                {{ number_format(($browser->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of pageviews') }}
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

                    <x-analytics::view view="cards" color="emerald" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($data as $browser)
                            <div class="bg-gradient-to-br from-emerald-900 to-emerald-950 rounded-xl shadow-lg p-5 flex flex-col justify-between group hover:from-emerald-800 hover:to-emerald-900 transition-all duration-300 hover:scale-[1.02] backdrop-blur-xl border border-emerald-800/30">
                                <div>
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="p-2.5 bg-emerald-800/50 rounded-xl group-hover:bg-emerald-700/50 transition-colors transform group-hover:rotate-6 duration-300 ring-1 ring-emerald-600/20">
                                            <img src="{{ asset('/vendor/analytics/icons/browsers/'.formatBrowser(strtolower($browser->value))) }}.svg" class="w-6 h-6 text-emerald-200">
                                        </div>
                                        <div class="text-xs font-medium text-emerald-400/80 group-hover:text-emerald-300/80 transition-colors">
                                            {{ $browser->updated_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold text-emerald-200 mb-2 group-hover:text-emerald-100 transition-colors">
                                        {{ $browser->value ? $browser->value : __('Unknown') }}
                                    </div>
                                </div>
                                <div class="mt-5">
                                    <div class="flex items-baseline space-x-2">
                                        <div class="text-3xl font-bold bg-gradient-to-r from-emerald-100 to-emerald-200 bg-clip-text text-transparent">{{ number_format($browser->count) }}</div>
                                        <div class="text-sm font-medium text-emerald-300/90 group-hover:text-emerald-200/90 transition-colors">{{ __('pageviews') }}</div>
                                    </div>
                                    <div class="text-xs font-medium text-emerald-400/70 group-hover:text-emerald-300/70 transition-colors mt-1">
                                        <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-emerald-300" />
                                        {{ number_format(($browser->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of total') }}
                                    </div>
                                    <div class="mt-4 h-1.5 bg-emerald-800/40 rounded-full overflow-hidden group-hover:bg-emerald-700/40 transition-colors ring-1 ring-emerald-600/10">
                                        <div class="h-full bg-gradient-to-r from-emerald-400 via-emerald-300 to-emerald-400 group-hover:from-emerald-300 group-hover:via-emerald-200 group-hover:to-emerald-300 transition-colors animate-pulse"
                                            style="width: {{ ($browser->count / $aggregates['total_count']) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-analytics::view>

                    <x-analytics::view view="compact" color="emerald" class="overflow-hidden rounded-xl border border-emerald-800 shadow-lg shadow-emerald-900/20">
                        <table class="min-w-full divide-y divide-emerald-800">
                            <thead class="bg-gradient-to-br from-emerald-900 to-emerald-950">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-emerald-300 uppercase tracking-wider cursor-pointer hover:text-emerald-200 transition-colors duration-200">
                                        <div class="flex items-center">
                                            <x-tooltip :text="__('Browser name')">
                                                <span><x-icon name="heroicon-o-globe-alt" class="w-4 h-4 inline mr-1" />{{ __('Browser') }}</span>
                                            </x-tooltip>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-emerald-300 uppercase tracking-wider">
                                        <x-tooltip :text="__('Total pageviews')">
                                            <span><x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />{{ __('Pageviews') }}</span>
                                        </x-tooltip>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-emerald-300 uppercase tracking-wider">
                                        <x-tooltip :text="__('Percentage of total pageviews')">
                                            <span><x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1" />{{ __('Usage') }}</span>
                                        </x-tooltip>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gradient-to-br from-emerald-950 to-emerald-900/90 divide-y divide-emerald-800/50">
                                @foreach($data as $browser)
                                    <tr class="hover:bg-emerald-900/50 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="bg-gradient-to-br from-emerald-700 to-emerald-900 p-2 rounded-full transform group-hover:rotate-12 transition-transform duration-300">
                                                    <img src="{{ asset('/vendor/analytics/icons/browsers/'.formatBrowser(strtolower($browser->value))) }}.svg" class="w-5 h-5 text-emerald-200">
                                                </div>
                                                <div class="text-sm font-medium text-emerald-100 group-hover:text-emerald-50 transition-colors">
                                                    {{ $browser->value ? $browser->value : __('Unknown') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-emerald-100">
                                            <x-tooltip :text="__('Total pageviews from this browser')">
                                                {{ number_format($browser->count, 0, __('.'), __(',')) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-tooltip :text="__('Percentage of total pageviews: :percent%', ['percent' => number_format(($browser->count / $aggregates['total_count']) * 100, 1)])">
                                                <div class="w-32 h-2.5 text-xs flex rounded-full bg-emerald-800/30 ml-auto overflow-hidden">
                                                    <div style="width: {{ ($browser->count / $aggregates['total_count']) * 100 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-300 hover:from-emerald-400 hover:to-emerald-300">
                                                    </div>
                                                </div>
                                                <div class="text-xs font-medium text-emerald-300 mt-1.5 text-right">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                    {{ number_format(($browser->count / $aggregates['total_count']) * 100, 1) }}%
                                                </div>
                                            </x-tooltip>
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
