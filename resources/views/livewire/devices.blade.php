<x-site :site="$site">
<x-loading />

    <div class="space-y-8">
        <x-breadcrumbs :breadcrumbs="[
            ['url' => route('sites.analytics.overview', ['site' => $site->id]), 'label' => __('Analytics Dashboard')],
            ['url' => route('sites.analytics.devices', ['site' => $site->id]), 'label' => __('Devices'), 'icon' => 'heroicon-o-device-phone-mobile']
        ]" />

        <div class="flex justify-end gap-4">
            <x-ts-dropdown position="bottom-end">
                <x-slot:action>
                    <x-ts-button x-on:click="show = !show" sm>{{ __('Time Range') }}</x-ts-button>
                </x-slot:action>
                <x-ts-dropdown.items wire:click="setTimeRange('today')" :active="$daterange === 'today'">{{ __('Today') }}</x-ts-dropdown.item>
                <x-ts-dropdown.items wire:click="setTimeRange('7d')" :active="$daterange === '7d'">{{ __('Last 7 Days') }}</x-ts-dropdown.item>
                <x-ts-dropdown.items wire:click="setTimeRange('30d')" :active="$daterange === '30d'">{{ __('Last 30 Days') }}</x-ts-dropdown.item>
                <x-ts-dropdown.items wire:click="setTimeRange('90d')" :active="$daterange === '90d'">{{ __('Last 90 Days') }}</x-ts-dropdown.item>
            </x-ts-dropdown>

            <x-ts-button wire:click="exportData" sm>
                <x-icon name="heroicon-o-arrow-down-tray" class="w-4 h-4 mr-2" />
                {{ __('Export') }}
            </x-ts-button>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div class="relative bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @dd($aggregates)
                    @foreach($this->overallStats as $section)
                        <div class="space-y-2">
                            @if($section['title'])
                                <h3 class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                    <x-icon name="{{ $section['icon'] }}" class="w-5 h-5 text-{{ $section['items'][0]['color'] }}-500" />
                                    {{ $section['title'] }}
                                </h3>
                            @endif

                            @foreach($section['items'] as $item)
                                @if($loop->first && !$section['title'])
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-{{ $item['color'] }}-50 dark:bg-{{ $item['color'] }}-800/20 rounded-lg">
                                            <x-icon name="{{ $item['icon'] }}" class="w-6 h-6 text-{{ $item['color'] }}-500" />
                                        </div>
                                        <div>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $item['label'] }}</p>
                                            <p class="text-xl font-semibold text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400">
                                                {{ number_format($aggregates[$item['key']]) }}
                                            </p>
                                        </div>
                                    </div>
                                @elseif($loop->index < 2)
                                    <div class="flex justify-between text-sm items-center">
                                        <span class="text-slate-500 flex items-center gap-2">
                                            <x-icon name="{{ $item['icon'] }}" class="w-4 h-4 text-{{ $item['color'] }}-500" />
                                            {{ $item['label'] }}:
                                        </span>
                                        <span class="font-medium text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400">
                                            @isset($aggregates[$item['key']])
                                                {{ number_format($aggregates[$item['key']], in_array($item['key'], ['visitor_retention_rate', 'peak_activity_percentage', 'most_common_percentage']) ? 1 : (in_array($item['key'], ['category_diversity_index']) ? 2 : 0)) }}
                                                @if(in_array($item['key'], ['visitor_retention_rate', 'peak_activity_percentage', 'most_common_percentage']))% @endif
                                            @else
                                                0
                                            @endisset
                                        </span>
                                    </div>
                                @endif
                            @endforeach

                            @if(count($section['items']) > 3)
                                <x-popover>
                                    <x-slot:trigger>
                                        <div class="flex items-center gap-2 text-sm font-medium text-slate-700 dark:text-slate-300 hover:text-primary-500 transition-colors cursor-pointer">
                                            <x-icon name="heroicon-o-ellipsis-horizontal" class="w-4 h-4" />
                                            <span class="text-sm">{{ __('Show More') }}</span>
                                        </div>
                                    </x-slot:trigger>
                                    <div class="space-y-2 p-2">
                                        @foreach(collect($section['items'])->slice(2) as $item)
                                            <div class="flex justify-between text-sm items-center">
                                                <span class="text-slate-500 flex items-center gap-2">
                                                    <x-icon name="{{ $item['icon'] }}" class="w-4 h-4 text-{{ $item['color'] }}-500" />
                                                    {{ $item['label'] }}:
                                                </span>
                                                <span class="font-medium text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400">
                                                    @isset($aggregates[$item['key']])
                                                        {{ number_format($aggregates[$item['key']], in_array($item['key'], ['visitor_retention_rate', 'peak_activity_percentage', 'most_common_percentage']) ? 1 : (in_array($item['key'], ['category_diversity_index']) ? 2 : 0)) }}
                                                        @if(in_array($item['key'], ['visitor_retention_rate', 'peak_activity_percentage', 'most_common_percentage']))% @endif
                                                    @else
                                                        0
                                                    @endisset
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </x-popover>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Visitor Frequency Section -->
                <div class="mt-8 pt-6 border-t border-slate-200/60 dark:border-slate-700/60">
                    <h3 class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-4">{{ __('Visitor Frequency') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        @foreach(['single_visit' => __('Single Visit'), 'returning' => __('Returning'), 'frequent' => __('Frequent')] as $key => $label)
                            <div class="bg-slate-50/50 dark:bg-slate-700/20 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-slate-500">{{ $label }}</span>
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                        {{ number_format($aggregates['visitor_frequency'][$key]) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if($devices->isNotEmpty())
            <div class="space-y-6">
                <div class="relative bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6" x-data="{ isOpen: true }">
                   <x-analytics::section-header
                        :title="__('Devices')"
                        :description="__('Detailed Insights Into Devices')"
                        :icon="'heroicon-o-device-phone-mobile'"
                    />
                    <div x-show="isOpen" x-collapse>
                        <div class="grid grid-cols-1 gap-6 mt-4">
                            @foreach($devices as $device)
                                <div class="relative bg-slate-50/50 dark:bg-slate-700/20 rounded-xl p-6 space-y-6">
                                    <x-analytics::cards.header
                                        :icon="'heroicon-o-device-phone-mobile'"
                                        :title="$device['device_brand'] . ' ' . $device['device_model']"
                                        :description="$device['device_type']"
                                        :visitors="$device['unique_visitors']"
                                        :visitors_label="__('Users')"
                                        :color="'blue'"
                                    />

                                    <div class="flex gap-4">
                                        @foreach($this->basicStats($device, $aggregates) as $stat)
                                            <x-analytics::cards.basic-stats :stat="$stat" class="flex-1"/>
                                        @endforeach
                                    </div>

                                    <div class="grid grid-cols-3 gap-6">
                                        <!-- Visit Stats -->
                                        <div class="col-span-1 space-y-4">
                                            @foreach($this->visitStats($device) as $stat)
                                                <x-analytics::cards.visit-stats :stat="$stat" />
                                            @endforeach
                                        </div>

                                        <!-- Hourly Distribution Chart -->
                                        <div class="col-span-2 bg-white/50 dark:bg-slate-800/50 rounded-lg">
                                            <x-analytics::distribution :distribution="$device['hourly_distribution']" title="Hourly Distribution" description="The hourly distribution shows the number of visits during each hour of the day."/>
                                        </div>
                                    </div>

                                    <!-- Device Details -->
                                    <div class="space-y-6">
                                        <div class="bg-white/50 dark:bg-slate-800/50 rounded-lg p-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                @foreach($this->deviceInfo($device) as $section)
                                                    <x-analytics::cards.info :section="$section" :data="$device" />
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <x-pagination :paginator="$devices" type="compact"/>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 bg-gradient-to-br from-slate-50 to-white dark:from-slate-900/90 dark:to-slate-800/90 rounded-2xl border border-dashed border-slate-200/60 dark:border-slate-700/60">
                <x-icon name="heroicon-o-device-phone-mobile" class="w-12 h-12 text-slate-400 mb-4"/>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">{{ __('No Device Data Available') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Start Monitoring To Collect Device Data') }}</p>
            </div>
        @endif
    </div>
</x-site>


@pushOnce('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endPushOnce
