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
                    'url' => route('websites.analytics.devices', ['website' => $site->id]),
                    'label' => __('Devices'),
                ]
            ]" />

        @include('analytics::livewire.partials.nav')
        <x-analytics::title
            :title="__('Devices')"
            :description="__('Devices are the devices used to access your website. This includes desktop computers, laptops, tablets, and mobile phones.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-device-phone-mobile'"
            :totalText="__('Total Devices')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('Device')]"
            :to="$to"
            :search="$search"
        />
        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: '{{ $display }}' }" class="space-y-4">
                    <x-analytics::view-switcher :data="$data" color="emerald" />

                    <x-analytics::view view="list" color="emerald" class="bg-gradient-to-br from-emerald-900 to-emerald-950 rounded-xl shadow-lg border border-emerald-800 p-6 backdrop-blur-xl">
                        <div class="flex flex-col space-y-6">
                            @foreach($data as $device)
                                <div class="flex items-center space-x-4 group hover:bg-emerald-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-emerald-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-emerald-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <x-tooltip text="{{ __('Device Type') }}: {{ Str::title($device->value) }}" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-emerald-200 to-emerald-100 bg-clip-text text-transparent">
                                                                    @if(strtolower($device->value) === 'desktop')
                                                                        <x-icon name="heroicon-o-computer-desktop" class="w-4 h-4 inline mr-1 text-emerald-300" />
                                                                    @elseif(strtolower($device->value) === 'mobile')
                                                                        <x-icon name="heroicon-o-device-phone-mobile" class="w-4 h-4 inline mr-1 text-emerald-300" />
                                                                    @elseif(strtolower($device->value) === 'tablet')
                                                                        <x-icon name="heroicon-o-device-tablet" class="w-4 h-4 inline mr-1 text-emerald-300" />
                                                                    @else
                                                                        <x-icon name="heroicon-o-question-mark-circle" class="w-4 h-4 inline mr-1 text-emerald-300" />
                                                                    @endif
                                                                    {{ Str::title($device->value) }}
                                                                </span>
                                                            </x-tooltip>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-emerald-700/30">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($device->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-300 hover:from-emerald-300 hover:to-emerald-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-emerald-300">
                                                    <div class="flex space-x-4">
                                                        <x-tooltip text="Total pageviews from this device type" class="group-hover:opacity-100">
                                                            <span class="hover:text-emerald-200 transition-colors">
                                                                <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1" />
                                                                {{ number_format($device->count, 0, __('.'), __(',')) }} {{ __('pageviews') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <div class="flex space-x-4">
                                                        <x-tooltip text="Percentage of total pageviews" class="group-hover:opacity-100">
                                                            <span class="hover:text-emerald-200 transition-colors">
                                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                                {{ $aggregates['total_count'] > 0 ? number_format(($device->count / $aggregates['total_count']) * 100, 1) : 0 }}% {{ __('of pageviews') }}
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
                        @foreach($data as $device)
                            <div class="bg-gradient-to-br from-emerald-900 to-emerald-950 rounded-xl shadow-lg border border-emerald-800 p-2 hover:scale-[1.02] transition-transform duration-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-emerald-800/20 blur-xl rounded-full"></div>
                                            <div class="relative bg-gradient-to-br from-emerald-700 to-emerald-900 p-3 rounded-full">
                                                @if(strtolower($device->value) === 'desktop')
                                                    <x-icon name="heroicon-o-computer-desktop" class="w-6 h-6 text-emerald-200" />
                                                @elseif(strtolower($device->value) === 'mobile')
                                                    <x-icon name="heroicon-o-device-phone-mobile" class="w-6 h-6 text-emerald-200" />
                                                @elseif(strtolower($device->value) === 'tablet')
                                                    <x-icon name="heroicon-o-device-tablet" class="w-6 h-6 text-emerald-200" />
                                                @else
                                                    <x-icon name="heroicon-o-question-mark-circle" class="w-6 h-6 text-emerald-200" />
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex flex-wrap gap-2 mb-2">
                                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-emerald-700/50 text-emerald-200 hover:bg-emerald-600/50 transition-colors duration-200">
                                                    {{ Str::title($device->value) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <div class="bg-emerald-800/20 rounded-lg p-3 hover:bg-emerald-700/30 transition-colors duration-200">
                                        <span class="text-emerald-300">{{ __('Pageviews') }}</span>
                                        <span class="text-emerald-100 font-semibold ml-2">{{ number_format($device->count, 0, __('.'), __(',')) }}</span>
                                    </div>
                                    <div class="relative pt-1">
                                        <div class="overflow-hidden h-3 text-xs flex rounded-lg bg-emerald-700/30">
                                            <div style="width: {{ $aggregates['total_count'] > 0 ? ($device->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                class="shadow-none bg-gradient-to-r from-emerald-400 to-emerald-600 transition-all duration-500 hover:from-emerald-500 hover:to-emerald-700">
                                            </div>
                                        </div>
                                        <div class="text-xs text-emerald-300 mt-2 text-right font-medium">
                                            <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                            {{ number_format(($device->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of total') }}
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
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-emerald-300 uppercase tracking-wider cursor-pointer hover:text-emerald-200 transition-colors duration-200" wire:click="$set('sort', '{{ $sort === 'asc' ? 'desc' : 'asc' }}'); $set('sortBy', 'value')">
                                        <div class="flex items-center">
                                            <x-tooltip text="{{ __('Sort by device type') }}">
                                                <span><x-icon name="heroicon-o-device-phone-mobile" class="w-4 h-4 inline mr-1" />{{ __('Device') }}</span>
                                            </x-tooltip>
                                            @if($sortBy === 'value')
                                                @if($sort === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-4 h-4 ml-1 text-emerald-200" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-4 h-4 ml-1 text-emerald-200" />
                                                @endif
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-emerald-300 uppercase tracking-wider cursor-pointer hover:text-emerald-200 transition-colors duration-200" wire:click="$set('sort', '{{ $sort === 'asc' ? 'desc' : 'asc' }}'); $set('sortBy', 'count')">
                                        <div class="flex items-center justify-end">
                                            <x-tooltip text="{{ __('Sort by number of pageviews') }}">
                                                <div class="flex items-center">
                                                    <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />
                                                    {{ __('Pageviews') }}
                                                </div>
                                            </x-tooltip>
                                            @if($sortBy === 'count')
                                                @if($sort === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-4 h-4 ml-1 text-emerald-200" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-4 h-4 ml-1 text-emerald-200" />
                                                @endif
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-emerald-300 uppercase tracking-wider">
                                        <x-tooltip text="{{ __('Percentage of total pageviews') }}">
                                            <span><x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1" />{{ __('Usage') }}</span>
                                        </x-tooltip>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gradient-to-br from-emerald-950 to-emerald-900/90 divide-y divide-emerald-800/50">
                                @foreach($data as $device)
                                    <tr class="hover:bg-emerald-900/50 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="text-sm text-emerald-100">
                                                    @if(strtolower($device->value) === 'desktop')
                                                        <x-icon name="heroicon-o-computer-desktop" class="w-4 h-4 inline mr-2 text-emerald-300" />
                                                    @elseif(strtolower($device->value) === 'mobile')
                                                        <x-icon name="heroicon-o-device-phone-mobile" class="w-4 h-4 inline mr-2 text-emerald-300" />
                                                    @elseif(strtolower($device->value) === 'tablet')
                                                        <x-icon name="heroicon-o-device-tablet" class="w-4 h-4 inline mr-2 text-emerald-300" />
                                                    @else
                                                        <x-icon name="heroicon-o-question-mark-circle" class="w-4 h-4 inline mr-2 text-emerald-300" />
                                                    @endif
                                                    {{ Str::title($device->value) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-emerald-100">
                                            <x-tooltip text="{{ __('Total pageviews from this device type') }}">
                                                {{ number_format($device->count, 0, __('.'), __(',')) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-tooltip text="{{ __('Percentage of total pageviews: :percent%', ['percent' => number_format(($device->count / $aggregates['total_count']) * 100, 1)]) }}">
                                                <div class="w-32 h-2.5 text-xs flex rounded-full bg-emerald-800/30 ml-auto overflow-hidden">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($device->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-emerald-500 to-emerald-400 transition-all duration-300 hover:from-emerald-400 hover:to-emerald-300">
                                                    </div>
                                                </div>
                                                <div class="text-xs font-medium text-emerald-300 mt-1.5 text-right">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                    {{ number_format(($device->count / $aggregates['total_count']) * 100, 1) }}%
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
