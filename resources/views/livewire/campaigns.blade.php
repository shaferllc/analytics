<x-website :website="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('websites.analytics.overview', ['website' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('websites.analytics.acquisitions', ['website' => $site->id]),
                'label' => __('Acquisitions'),
            ],
            [
                'url' => route('websites.analytics.campaigns', ['website' => $site->id]),
                'label' => __('Campaigns'),
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Campaigns')"
            :description="__('Track marketing campaign performance and visitor sources.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-megaphone'"
            :totalText="__('Total Campaigns')"
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
            :sortWords="['count' => __('Visitors'), 'value' => __('Campaign')]"
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
                            @foreach($data as $campaign)
                                <div class="flex items-center space-x-4 group hover:bg-indigo-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-indigo-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-indigo-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <x-tooltip :text="__('Campaign')" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-indigo-200 to-indigo-100 bg-clip-text text-transparent">
                                                                    <x-icon name="heroicon-o-megaphone" class="w-4 h-4 inline mr-1 text-indigo-300" />
                                                                    {{ $campaign->value ?: __('Unknown') }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <x-tooltip :text="__('Total visitors from this campaign')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-users" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ number_format($campaign->count, 0, __('.'), __(',')) }} {{ __('visitors') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-indigo-700/30">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($campaign->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all duration-300 hover:from-indigo-300 hover:to-indigo-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-indigo-300">
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('First seen')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ $campaign->created_at->diffForHumans() }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('Last seen')" class="group-hover:opacity-100">
                                                            <span class="hover:text-indigo-200 transition-colors">
                                                                <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                                {{ $campaign->updated_at->diffForHumans() }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <x-tooltip :text="__('Percentage of total visitors')" class="group-hover:opacity-100">
                                                        <span class="hover:text-indigo-200 transition-colors">
                                                            <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                            {{ $aggregates['total_count'] > 0 ? number_format(($campaign->count / $aggregates['total_count']) * 100, 1) : 0 }}%
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-300 uppercase tracking-wider">Campaign</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">Visitors</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">Sessions</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">Graph</th>
                                </tr>
                            </thead>
                            <tbody class="bg-indigo-950 divide-y divide-indigo-800">
                                @foreach($data as $campaign)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <x-icon name="heroicon-o-megaphone" class="w-4 h-4 text-indigo-300 mr-2" />
                                                <div class="text-sm font-medium text-indigo-100 max-w-xs" x-data="{ showFull: false }" @mouseenter="showFull = true" @mouseleave="showFull = false">
                                                    <x-tooltip :text="__('Campaign name')">
                                                        <span x-show="showFull" class="break-all">{{ $campaign->value ?: __('Unknown') }}</span>
                                                        <span x-show="!showFull" x-cloak>{{ Str::limit($campaign->value, 70) ?: __('Unknown') }}</span>
                                                    </x-tooltip>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-indigo-100">
                                            <x-tooltip :text="__('Total visitors from this campaign')">
                                                {{ number_format($campaign->count, 0, __('.'), __(',')) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-indigo-100">
                                            <x-tooltip :text="__('Percentage of total visitors')">
                                                {{ number_format(($campaign->count / $aggregates['total_count']) * 100, 1) }}%
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-tooltip :text="__('Percentage of total visitors: :percent%', ['percent' => number_format(($campaign->count / $aggregates['total_count']) * 100, 1)])">
                                                <div class="w-32 h-2.5 text-xs flex rounded-full bg-indigo-800/30 ml-auto overflow-hidden">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($campaign->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-indigo-500 to-indigo-400 transition-all duration-300 hover:from-indigo-400 hover:to-indigo-300">
                                                    </div>
                                                </div>
                                                <div class="text-xs font-medium text-indigo-300 mt-1.5 text-right">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                    {{ number_format(($campaign->count / $aggregates['total_count']) * 100, 1) }}%
                                                </div>
                                            </x-tooltip>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>

                    <x-analytics::view view="cards" color="indigo" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($data as $campaign)
                            <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-2 hover:scale-[1.02] transition-transform duration-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-indigo-800/20 blur-xl rounded-full"></div>
                                            <div class="relative bg-gradient-to-br from-indigo-700 to-indigo-900 p-3 rounded-full">
                                                <x-tooltip :text="__('Campaign')">
                                                    <x-icon name="heroicon-o-megaphone" class="w-6 h-6 text-indigo-200" />
                                                </x-tooltip>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm text-indigo-100 break-all hover:text-indigo-300 transition-colors duration-200">
                                                <x-tooltip :text="__('Campaign Name')">
                                                    <x-icon name="heroicon-o-tag" class="w-3 h-3 inline mr-1" />
                                                    {{ $campaign->value ?: __('Unknown') }}
                                                </x-tooltip>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <x-tooltip :text="__('Total visitors from this campaign')">
                                            <div class="bg-indigo-800/20 rounded-lg p-3 hover:bg-indigo-700/30 transition-colors duration-200">
                                                <span class="text-indigo-300">
                                                    <x-icon name="heroicon-o-users" class="w-3 h-3 inline mr-1" />
                                                    {{ __('Visitors') }}
                                                </span>
                                                <span class="text-indigo-100 font-semibold ml-2">{{ number_format($campaign->count, 0, __('.'), __(',')) }}</span>
                                            </div>
                                        </x-tooltip>
                                        <x-tooltip :text="__('Percentage of total visitors')">
                                            <div class="bg-indigo-800/20 rounded-lg p-3 hover:bg-indigo-700/30 transition-colors duration-200">
                                                <span class="text-indigo-300">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                    {{ __('Share') }}
                                                </span>
                                                <span class="text-indigo-100 font-semibold ml-2">{{ number_format(($campaign->count / $aggregates['total_count']) * 100, 1) }}%</span>
                                            </div>
                                        </x-tooltip>
                                    </div>
                                    <div class="relative pt-1">
                                        <div class="overflow-hidden h-3 text-xs flex rounded-lg bg-indigo-700/30">
                                            <div style="width: {{ $aggregates['total_count'] > 0 ? ($campaign->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                class="shadow-none bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all duration-500 hover:from-indigo-500 hover:to-indigo-700">
                                            </div>
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
