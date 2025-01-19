<x-website :website="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('websites.analytics.overview', ['website' => $site->id]),
                'label' => __('Dashboard'),
                'icon' => 'heroicon-o-home'
            ],
            [
                'url' => route('websites.analytics.acquisitions', ['website' => $site->id]),
                'label' => __('Acquisitions'),
                'icon' => 'heroicon-o-arrow-trending-up'
            ],
            [
                'url' => route('websites.analytics.social-networks', ['website' => $site->id]),
                'label' => __('Social Networks'),
                'icon' => 'heroicon-o-share'
            ]
        ]" />

        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Social Networks')"
            :description="__('Social networks used to access your website.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-share'"
            :totalText="__('Total Social Networks')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('Social Network')]"
            :to="$to"
            :search="$search"
        />

        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: '{{ $display }}' }" class="space-y-4">
                    <x-analytics::view-switcher :data="$data" color="indigo" />

                    <x-analytics::view view="list" color="indigo" class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6 backdrop-blur-xl">
                        <div class="flex flex-col space-y-6">
                            @foreach($data as $socialNetwork)
                                <div class="flex items-center space-x-4 group hover:bg-indigo-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-indigo-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-indigo-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <x-tooltip :text="__('Social Network')" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-indigo-200 to-indigo-100 bg-clip-text text-transparent">
                                                                    <x-icon name="heroicon-o-share" class="w-4 h-4 inline mr-1 text-indigo-300" />
                                                                    {{ $socialNetwork->value ?: __('Unknown') }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <x-tooltip :text="__('Total pageviews from this social network')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ number_format($socialNetwork->count, 0, __('.'), __(',')) }} {{ __('pageviews') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-indigo-700/30">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($socialNetwork->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all duration-300 hover:from-indigo-300 hover:to-indigo-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-indigo-300">
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('First seen')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ $socialNetwork->created_at->diffForHumans() }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('Last seen')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ $socialNetwork->updated_at->diffForHumans() }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <x-tooltip :text="__('Percentage of total pageviews')" class="group-hover:opacity-100">
                                                        <span class="hover:text-indigo-200 transition-colors">
                                                            <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                            {{ $aggregates['total_count'] > 0 ? number_format(($socialNetwork->count / $aggregates['total_count']) * 100, 1) : 0 }}%
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

                    <x-analytics::view view="compact" color="indigo" class="overflow-hidden rounded-xl border border-indigo-800 shadow-lg shadow-indigo-900/20">
                        <table class="min-w-full divide-y divide-indigo-800">
                            <thead class="bg-indigo-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-300 uppercase tracking-wider">
                                        <x-icon name="heroicon-o-share" class="w-4 h-4 inline mr-1" />
                                        {{ __('Social Network') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">
                                        <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />
                                        {{ __('Pageviews') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">
                                        <x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1" />
                                        {{ __('Percentage') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">
                                        {{ __('Graph') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-indigo-950 divide-y divide-indigo-800">
                                @foreach($data as $socialNetwork)
                                    <tr class="hover:bg-indigo-900/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-indigo-100" x-data="{ showFull: false }" @mouseenter="showFull = true" @mouseleave="showFull = false">
                                                    <x-tooltip :text="__('Social Network Name')">
                                                        <span x-show="showFull" class="break-all">{{ $socialNetwork->value ?: __('Unknown') }}</span>
                                                        <span x-show="!showFull" x-cloak>{{ Str::limit($socialNetwork->value, 70) ?: __('Unknown') }}</span>
                                                    </x-tooltip>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-indigo-100">
                                            <x-tooltip :text="__('Total pageviews from this social network')">
                                                {{ number_format($socialNetwork->count, 0, __('.'), __(',')) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-indigo-100">
                                            <x-tooltip :text="__('Percentage of total pageviews')">
                                                {{ number_format(($socialNetwork->count / $aggregates['total_count']) * 100, 1) }}%
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-tooltip :text="__('Visual representation of pageview share')">
                                                <div class="w-32 h-2.5 text-xs flex rounded-full bg-indigo-800/30 ml-auto overflow-hidden">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($socialNetwork->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-indigo-500 to-indigo-400 transition-all duration-300 hover:from-indigo-400 hover:to-indigo-300">
                                                    </div>
                                                </div>
                                            </x-tooltip>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>

                    <x-analytics::view view="cards" color="indigo" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($data as $socialNetwork)
                            <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="bg-gradient-to-br from-indigo-700 to-indigo-900 p-2 rounded-full transform group-hover:rotate-12 transition-transform duration-300">
                                            <x-icon name="heroicon-o-share" class="w-5 h-5 text-indigo-200" />
                                        </div>
                                        <div class="text-sm font-medium text-indigo-100">
                                            <x-tooltip :text="__('Social Network Name')">
                                                {{ $socialNetwork->value ?: __('Unknown') }}
                                            </x-tooltip>
                                        </div>
                                    </div>
                                    <x-tooltip :text="__('Total pageviews from this social network')">
                                        <div class="text-2xl font-bold text-indigo-100">
                                            <x-icon name="heroicon-o-eye" class="w-5 h-5 inline mr-1" />
                                            {{ number_format($socialNetwork->count, 0, __('.'), __(',')) }}
                                        </div>
                                    </x-tooltip>
                                </div>

                                <div class="space-y-3">
                                    <x-tooltip :text="__('Visual representation of pageview share')">
                                        <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-indigo-700/30">
                                            <div style="width: {{ $aggregates['total_count'] > 0 ? ($socialNetwork->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                class="shadow-lg bg-gradient-to-r from-indigo-400 to-indigo-600">
                                            </div>
                                        </div>
                                    </x-tooltip>

                                    <div class="flex justify-between text-xs text-indigo-300">
                                        <div class="flex space-x-4">
                                            <x-tooltip :text="__('First seen')">
                                                <span class="hover:text-indigo-200 transition-colors">
                                                    <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                    {{ $socialNetwork->created_at->diffForHumans() }}
                                                </span>
                                            </x-tooltip>
                                            <x-tooltip :text="__('Last seen')">
                                                <span class="hover:text-indigo-200 transition-colors">
                                                    <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                    {{ $socialNetwork->updated_at->diffForHumans() }}
                                                </span>
                                            </x-tooltip>
                                        </div>
                                        <x-tooltip :text="__('Percentage of total pageviews')">
                                            <span class="hover:text-indigo-200 transition-colors">
                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                {{ number_format(($socialNetwork->count / $aggregates['total_count']) * 100, 1) }}%
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
