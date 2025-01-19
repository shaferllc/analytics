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
                    'url' => route('websites.analytics.screen-resolutions', ['website' => $site->id]),
                    'label' => __('Screen Resolutions'),
                ]
            ]" />

        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Screen Resolutions')"
            :description="__('Screen resolutions are the dimensions of screens used to access your website. The @2x and @3x suffixes indicate high-density displays that use multiple physical pixels per logical pixel - @2x means 2 physical pixels per logical pixel, @3x means 3 physical pixels per logical pixel.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-computer-desktop'"
            :totalText="__('Total Screen Resolutions')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('Resolution')]"
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
                     <x-analytics::view-switcher :data="$data" color="fuchsia" />

                    <x-analytics::view view="list" color="fuchsia" class="bg-gradient-to-br from-fuchsia-900 to-fuchsia-950 rounded-xl shadow-lg border border-fuchsia-800 p-6 backdrop-blur-xl">
                        <div class="flex flex-col space-y-6">
                            @foreach($data as $screenResolution)
                                <div class="flex items-center space-x-4 group hover:bg-fuchsia-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-fuchsia-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-fuchsia-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <x-tooltip text="{{ str_contains($screenResolution->value, '@2x') ? 'High-density display using 2 physical pixels per logical pixel' : (str_contains($screenResolution->value, '@3x') ? 'High-density display using 3 physical pixels per logical pixel' : 'Standard display resolution') }}" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-fuchsia-200 to-fuchsia-100 bg-clip-text text-transparent">
                                                                    <x-icon name="heroicon-o-computer-desktop" class="w-4 h-4 inline mr-1 text-fuchsia-300" />
                                                                    {{ $screenResolution->value ? $screenResolution->value : __('Unknown') }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                        <div class="flex items-center space-x-2 mt-1">
                                                            <x-tooltip text="First seen {{ $screenResolution->created_at->diffForHumans() }}" class="group-hover:opacity-100">
                                                                <span class="text-xs text-fuchsia-400 hover:text-fuchsia-300 transition-colors">
                                                                    <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1 text-fuchsia-300" />
                                                                    {{ __('First seen') }} {{ $screenResolution->created_at->diffForHumans() }}
                                                                </span>
                                                            </x-tooltip>
                                                            <x-tooltip text="Last seen {{ $screenResolution->updated_at->diffForHumans() }}" class="group-hover:opacity-100">
                                                                <span class="text-xs text-fuchsia-400 hover:text-fuchsia-300 transition-colors">
                                                                    <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1 text-fuchsia-300" />
                                                                    {{ __('Last seen') }} {{ $screenResolution->updated_at->diffForHumans() }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <x-tooltip text="Average daily pageviews" class="group-hover:opacity-100">
                                                            <span class="px-2 py-0.5 text-xs rounded-full bg-fuchsia-700 hover:bg-fuchsia-600 transition-colors duration-300">
                                                                <x-icon name="heroicon-o-chart-bar" class="w-3 h-3 inline mr-1" />
                                                                {{ round($screenResolution->count / max(1, (strtotime($screenResolution->updated_at) - strtotime($screenResolution->created_at)) / 86400), 1) }} {{ __('views/day') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-fuchsia-700/30">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($screenResolution->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-fuchsia-400 to-fuchsia-600 transition-all duration-300 hover:from-fuchsia-300 hover:to-fuchsia-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-fuchsia-300">
                                                    <div class="flex space-x-4">
                                                        <x-tooltip text="Total pageviews with this resolution" class="group-hover:opacity-100">
                                                            <span class="hover:text-fuchsia-200 transition-colors">
                                                                <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1" />
                                                                {{ number_format($screenResolution->count, 0, __('.'), __(',')) }} {{ __('pageviews') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <div class="flex space-x-4">
                                                        <x-tooltip text="Percentage of total pageviews" class="group-hover:opacity-100">
                                                            <span class="hover:text-fuchsia-200 transition-colors">
                                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                                {{ $aggregates['total_count'] > 0 ? number_format(($screenResolution->count / $aggregates['total_count']) * 100, 1) : 0 }}% {{ __('of pageviews') }}
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

                    <x-analytics::view view="cards" color="fuchsia" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($data as $screenResolution)
                            <div class="bg-gradient-to-br from-fuchsia-900 to-fuchsia-950 rounded-xl shadow-lg border border-fuchsia-800 p-2 hover:scale-[1.02] transition-transform duration-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-fuchsia-800/20 blur-xl rounded-full"></div>
                                            <div class="relative bg-gradient-to-br from-fuchsia-700 to-fuchsia-900 p-3 rounded-full">
                                                <x-icon name="heroicon-o-computer-desktop" class="w-6 h-6 text-fuchsia-200" />
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex flex-wrap gap-2 mb-2">
                                                <x-tooltip text="{{ str_contains($screenResolution->value, '@2x') ? 'High-density display using 2 physical pixels per logical pixel' : (str_contains($screenResolution->value, '@3x') ? 'High-density display using 3 physical pixels per logical pixel' : 'Standard display resolution') }}">
                                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-fuchsia-700/50 text-fuchsia-200 hover:bg-fuchsia-600/50 transition-colors duration-200">
                                                        <x-icon name="heroicon-o-computer-desktop" class="w-3 h-3 inline mr-1" />
                                                        {{ $screenResolution->value ? $screenResolution->value : __('Unknown') }}
                                                    </span>
                                                </x-tooltip>
                                            </div>
                                            <div class="text-xs text-fuchsia-400 break-all hover:text-fuchsia-300 transition-colors duration-200">
                                                <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1" />
                                                {{ __('First seen') }} {{ $screenResolution->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <x-tooltip text="Total pageviews with this resolution">
                                            <div class="bg-fuchsia-800/20 rounded-lg p-3 hover:bg-fuchsia-700/30 transition-colors duration-200">
                                                <span class="text-fuchsia-300">
                                                    <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1" />
                                                    {{ __('Pageviews') }}
                                                </span>
                                                <span class="text-fuchsia-100 font-semibold ml-2">{{ number_format($screenResolution->count, 0, __('.'), __(',')) }}</span>
                                            </div>
                                        </x-tooltip>
                                        <x-tooltip text="Average daily pageviews">
                                            <div class="bg-fuchsia-800/20 rounded-lg p-3 hover:bg-fuchsia-700/30 transition-colors duration-200">
                                                <span class="text-fuchsia-300">
                                                    <x-icon name="heroicon-o-chart-bar" class="w-3 h-3 inline mr-1" />
                                                    {{ __('Daily Avg') }}
                                                </span>
                                                <span class="text-fuchsia-100 font-semibold ml-2">{{ round($screenResolution->count / max(1, (strtotime($screenResolution->updated_at) - strtotime($screenResolution->created_at)) / 86400), 1) }}</span>
                                            </div>
                                        </x-tooltip>
                                    </div>
                                    <div class="relative pt-1">
                                        <div class="overflow-hidden h-3 text-xs flex rounded-lg bg-fuchsia-700/30">
                                            <div style="width: {{ $aggregates['total_count'] > 0 ? ($screenResolution->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                class="shadow-none bg-gradient-to-r from-fuchsia-400 to-fuchsia-600 transition-all duration-500 hover:from-fuchsia-500 hover:to-fuchsia-700">
                                            </div>
                                        </div>
                                        <x-tooltip text="Percentage of total pageviews">
                                            <div class="text-xs text-fuchsia-300 mt-2 text-right font-medium">
                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                {{ number_format(($screenResolution->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of total') }}
                                            </div>
                                        </x-tooltip>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-analytics::view>

                    <x-analytics::view view="compact" color="fuchsia" class="overflow-hidden rounded-xl border border-fuchsia-800 shadow-lg shadow-fuchsia-900/20">
                        <table class="min-w-full divide-y divide-fuchsia-800">
                            <thead class="bg-gradient-to-br from-fuchsia-900 to-fuchsia-950">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-fuchsia-300 uppercase tracking-wider cursor-pointer hover:text-fuchsia-200 transition-colors duration-200" wire:click="$set('sort', '{{ $sort === 'asc' ? 'desc' : 'asc' }}'); $set('sortBy', 'value')">
                                        <div class="flex items-center">
                                            <x-tooltip text="Sort by resolution">
                                                <span><x-icon name="heroicon-o-computer-desktop" class="w-4 h-4 inline mr-1" />{{ __('Resolution') }}</span>
                                            </x-tooltip>
                                            @if($sortBy === 'value')
                                                @if($sort === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-4 h-4 ml-1 text-fuchsia-200" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-4 h-4 ml-1 text-fuchsia-200" />
                                                @endif
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-fuchsia-300 uppercase tracking-wider cursor-pointer hover:text-fuchsia-200 transition-colors duration-200" wire:click="$set('sort', '{{ $sort === 'asc' ? 'desc' : 'asc' }}'); $set('sortBy', 'count')">
                                        <div class="flex items-center justify-end">
                                            <x-tooltip text="Sort by number of pageviews">
                                                <div class="flex items-center">
                                                    <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />
                                                    {{ __('Pageviews') }}
                                                </div>
                                            </x-tooltip>
                                            @if($sortBy === 'count')
                                                @if($sort === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-4 h-4 ml-1 text-fuchsia-200" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-4 h-4 ml-1 text-fuchsia-200" />
                                                @endif
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-fuchsia-300 uppercase tracking-wider">
                                        <x-tooltip text="Average daily pageviews">
                                            <span><x-icon name="heroicon-o-chart-bar" class="w-4 h-4 inline mr-1" />{{ __('Daily Avg') }}</span>
                                        </x-tooltip>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-fuchsia-300 uppercase tracking-wider">
                                        <x-tooltip text="First seen">
                                            <span><x-icon name="heroicon-o-clock" class="w-4 h-4 inline mr-1" />{{ __('First Seen') }}</span>
                                        </x-tooltip>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-fuchsia-300 uppercase tracking-wider">
                                        <x-tooltip text="Last seen">
                                            <span><x-icon name="heroicon-o-arrow-path" class="w-4 h-4 inline mr-1" />{{ __('Last Seen') }}</span>
                                        </x-tooltip>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-fuchsia-300 uppercase tracking-wider">
                                        <x-tooltip text="Percentage of total pageviews">
                                            <span><x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1" />{{ __('Usage') }}</span>
                                        </x-tooltip>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gradient-to-br from-fuchsia-950 to-fuchsia-900/90 divide-y divide-fuchsia-800/50">
                                @foreach($data as $screenResolution)
                                    <tr class="hover:bg-fuchsia-900/50 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="text-sm text-fuchsia-100">
                                                    <x-tooltip text="{{ str_contains($screenResolution->value, '@2x') ? 'High-density display using 2 physical pixels per logical pixel' : (str_contains($screenResolution->value, '@3x') ? 'High-density display using 3 physical pixels per logical pixel' : 'Standard display resolution') }}">
                                                        <span>
                                                            <x-icon name="heroicon-o-computer-desktop" class="w-4 h-4 inline mr-1" />
                                                            {{ $screenResolution->value ? $screenResolution->value : __('Unknown') }}
                                                        </span>
                                                    </x-tooltip>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-fuchsia-100">
                                            <x-tooltip text="Total pageviews with this resolution">
                                                {{ number_format($screenResolution->count, 0, __('.'), __(',')) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-fuchsia-100">
                                            <x-tooltip text="Average daily pageviews">
                                                {{ round($screenResolution->count / max(1, (strtotime($screenResolution->updated_at) - strtotime($screenResolution->created_at)) / 86400), 1) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-fuchsia-700/60 to-fuchsia-600/60 text-fuchsia-100 shadow-sm">
                                                <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1" />
                                                {{ $screenResolution->created_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-fuchsia-700/60 to-fuchsia-600/60 text-fuchsia-100 shadow-sm">
                                                <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1" />
                                                {{ $screenResolution->updated_at->diffForHumans() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-tooltip text="Percentage of total pageviews: {{ number_format(($screenResolution->count / $aggregates['total_count']) * 100, 1) }}%">
                                                <div class="w-32 h-2.5 text-xs flex rounded-full bg-fuchsia-800/30 ml-auto overflow-hidden">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($screenResolution->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-fuchsia-500 to-fuchsia-400 transition-all duration-300 hover:from-fuchsia-400 hover:to-fuchsia-300">
                                                    </div>
                                                </div>
                                                <div class="text-xs font-medium text-fuchsia-300 mt-1.5 text-right">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                    {{ number_format(($screenResolution->count / $aggregates['total_count']) * 100, 1) }}%
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
