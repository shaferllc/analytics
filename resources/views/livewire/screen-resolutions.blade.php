<x-site :site="$site">
    <div class="space-y-8">
        <x-breadcrumbs :breadcrumbs="[
            ['url' => route('sites.analytics.overview', ['site' => $site->id]), 'label' => __('Analytics Dashboard')],
            ['url' => route('sites.analytics.screen-resolutions', ['site' => $site->id]), 'label' => __('Screen Resolutions'), 'icon' => 'heroicon-o-device-tablet']
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

        <x-loading />
        @if($resolutions->isNotEmpty())
            <div class="space-y-6">
                <div class="relative bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6 overflow-hidden" x-data="{ isOpen: true }">
                    <div class="cursor-pointer" @click="isOpen = !isOpen">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="space-y-2 flex-initial">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                        <x-icon name="heroicon-o-device-tablet" class="w-6 h-6 text-emerald-500" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
                                            {{ __('Screen Resolutions') }}
                                        </h2>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            {{ __('Detailed Insights Into User Screen Resolutions') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 p-2 hover:bg-slate-100/50 dark:hover:bg-slate-700/20 rounded-lg transition-colors">
                                <div>
                                    <template x-if="isOpen">
                                        <x-icon name="heroicon-o-chevron-up" class="w-6 h-6 text-slate-400 bg-slate-100/50 dark:bg-slate-500/20 rounded-lg p-1"  />
                                    </template>
                                    <template x-if="!isOpen">
                                        <x-icon name="heroicon-o-chevron-down" class="w-6 h-6 text-slate-400 bg-slate-100/50 dark:bg-slate-500/20 rounded-lg p-1" />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div x-show="isOpen" x-collapse>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="relative bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <!-- Basic Stats -->
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-emerald-50 dark:bg-emerald-800/20 rounded-lg">
                                                <x-icon name="heroicon-o-device-tablet" class="w-6 h-6 text-emerald-500" />
                                            </div>
                                            <div>
                                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Total Resolutions') }}</p>
                                                <p class="text-xl font-semibold text-emerald-600 dark:text-emerald-400">
                                                    {{ number_format($aggregates['total_categories']) }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-users" class="w-4 h-4 text-blue-500" />
                                                {{ __('Unique Visitors') }}:
                                            </span>
                                            <span class="font-medium text-blue-600 dark:text-blue-400">{{ number_format($aggregates['unique_visitors']) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 text-purple-500" />
                                                {{ __('Total Visits') }}:
                                            </span>
                                            <span class="font-medium text-purple-600 dark:text-purple-400">{{ number_format($aggregates['total_visits']) }}</span>
                                        </div>
                                    </div>

                                    <!-- Engagement Metrics -->
                                    <div class="space-y-2">
                                        <h3 class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                            <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 text-indigo-500" />
                                            {{ __('Engagement') }}
                                        </h3>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-arrow-trending-up" class="w-4 h-4 text-sky-500" />
                                                {{ __('Avg Visits/Resolution') }}:
                                            </span>
                                            <span class="font-medium text-sky-600 dark:text-sky-400">{{ number_format($aggregates['average_visits_per_visitor'], 1) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-arrow-uturn-left" class="w-4 h-4 text-teal-500" />
                                                {{ __('Retention Rate') }}:
                                            </span>
                                            <span class="font-medium text-teal-600 dark:text-teal-400">{{ number_format($aggregates['visitor_retention_rate'], 1) }}%</span>
                                        </div>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-star" class="w-4 h-4 text-amber-500" />
                                                {{ __('Engagement Score') }}:
                                            </span>
                                            <span class="font-medium text-amber-600 dark:text-amber-400">{{ number_format($aggregates['engagement_score'], 1) }}</span>
                                        </div>
                                    </div>

                                    <!-- Visit Distribution -->
                                    <div class="space-y-2">
                                        <h3 class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                            <x-icon name="heroicon-o-chart-pie" class="w-5 h-5 text-rose-500" />
                                            {{ __('Visit Patterns') }}
                                        </h3>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-user" class="w-4 h-4 text-red-500" />
                                                {{ __('Single Visit Resolutions') }}:
                                            </span>
                                            <span class="font-medium text-red-600 dark:text-red-400">{{ number_format($aggregates['categories_with_single_visit']) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-users" class="w-4 h-4 text-orange-500" />
                                                {{ __('Multiple Visit Resolutions') }}:
                                            </span>
                                            <span class="font-medium text-orange-600 dark:text-orange-400">{{ number_format($aggregates['categories_with_multiple_visits']) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-bolt" class="w-4 h-4 text-yellow-500" />
                                                {{ __('Peak Activity') }}:
                                            </span>
                                            <span class="font-medium text-yellow-600 dark:text-yellow-400">{{ number_format($aggregates['peak_activity_percentage'], 1) }}%</span>
                                        </div>
                                    </div>

                                    <!-- Advanced Metrics -->
                                    <div class="space-y-2">
                                        <h3 class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                            <x-icon name="heroicon-o-beaker" class="w-5 h-5 text-fuchsia-500" />
                                            {{ __('Advanced Metrics') }}
                                        </h3>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-variable" class="w-4 h-4 text-pink-500" />
                                                {{ __('Diversity Index') }}:
                                            </span>
                                            <span class="font-medium text-pink-600 dark:text-pink-400">{{ number_format($aggregates['category_diversity_index'], 2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-document-text" class="w-4 h-4 text-violet-500" />
                                                {{ __('Metadata Coverage') }}:
                                            </span>
                                            <span class="font-medium text-violet-600 dark:text-violet-400">{{ number_format(($aggregates['categories_with_meta_data'] / max(1, $aggregates['categories_with_meta_data'] + $aggregates['categories_without_meta_data'])) * 100, 1) }}%</span>
                                        </div>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-clipboard-document-list" class="w-4 h-4 text-cyan-500" />
                                                {{ __('Avg Meta/Resolution') }}:
                                            </span>
                                            <span class="font-medium text-cyan-600 dark:text-cyan-400">{{ number_format($aggregates['average_meta_data_per_category'], 1) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm items-center">
                                            <span class="text-slate-500 flex items-center gap-2">
                                                <x-icon name="heroicon-o-clock" class="w-4 h-4 text-lime-500" />
                                                {{ __('Active Hours/Day') }}:
                                            </span>
                                            <span class="font-medium text-lime-600 dark:text-lime-400">{{ number_format($aggregates['average_active_hours'] ?? 0, 1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4" x-data="{ openCards: {} }">
                            @foreach($resolutions as $resolution)
                                <div class="relative bg-slate-50/50 dark:bg-slate-700/20 rounded-xl p-6"
                                    x-data="{
                                        index: {{ $loop->index }},
                                        get isOpen() {
                                            return $data.openCards[this.index] || false;
                                        },
                                        toggle() {
                                            $data.openCards[this.index] = !this.isOpen;
                                            if (this.isOpen) {
                                                // Open adjacent card if it exists
                                                const adjacentIndex = this.index % 2 === 0 ? this.index + 1 : this.index - 1;
                                                $data.openCards[adjacentIndex] = true;
                                            }
                                        }
                                    }"
                                >
                                    <!-- Header with Resolution and User Count -->
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <x-icon name="{{ in_array('mobile', $resolution['meta_data']['device-type']['distribution'] ?? []) ? 'heroicon-o-device-phone-mobile' : 'heroicon-o-device-tablet' }}"
                                                   class="w-6 h-6 text-slate-400" />
                                            <div>
                                                <span class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $resolution['value'] }}</span>
                                                <p class="text-sm text-slate-500">{{ number_format($resolution['percentage'], 1) }}% of total</p>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-800/30 dark:text-emerald-200">
                                                {{ number_format($resolution['unique_visitors']) }} {{ __('Users') }}
                                            </span>
                                            <span class="text-sm text-slate-500">{{ number_format($resolution['total_visits']) }} {{ __('visits') }}</span>
                                        </div>
                                    </div>

                                    <!-- Quick Stats Grid - Always Visible -->
                                    <div class="grid grid-cols-3 gap-4 mb-4">
                                        <div class="bg-white dark:bg-slate-800 rounded-lg p-3">
                                            <p class="text-xs text-slate-500">{{ __('First Seen') }}</p>
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                {{ Carbon\Carbon::parse($resolution['first_seen'])->format('M j, Y') }}
                                            </p>
                                        </div>
                                        <div class="bg-white dark:bg-slate-800 rounded-lg p-3">
                                            <p class="text-xs text-slate-500">{{ __('Visits/User') }}</p>
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                {{ number_format($resolution['visits_per_visitor'], 1) }}
                                            </p>
                                        </div>
                                        <div class="bg-white dark:bg-slate-800 rounded-lg p-3">
                                            <p class="text-xs text-slate-500">{{ __('Active Days') }}</p>
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                {{ number_format($resolution['visit_days']) }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Most Common Device Type - Always Visible -->
                                    @if(!empty($resolution['meta_data']['device-type']['most_common']))
                                        <div class="flex items-center gap-2 mb-4 bg-white dark:bg-slate-800 rounded-lg p-3">
                                            <span class="text-sm text-slate-500">{{ __('Most Common Device') }}:</span>
                                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                                {{ ucfirst($resolution['meta_data']['device-type']['most_common']) }}
                                            </span>
                                            <span class="text-xs px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded">
                                                {{ number_format($resolution['meta_data']['device-type']['most_common_percentage'] ?? 0, 1) }}%
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Show More Button -->
                                    <button
                                        @click="toggle()"
                                        class="w-full py-2 px-4 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700/50 rounded-lg transition-colors flex items-center justify-center gap-2"
                                    >
                                        <span x-text="isOpen ? '{{ __('Show Less') }}' : '{{ __('Show More Details') }}'"></span>
                                        <x-icon
                                            :name="'heroicon-o-chevron-down'"
                                            class="w-4 h-4 transition-transform"
                                            ::class="isOpen ? 'rotate-180' : ''"
                                        />
                                    </button>

                                    <!-- Detailed Content -->
                                    <div x-show="isOpen" x-collapse class="mt-4 space-y-4">
                                        <!-- Visit Days and Percentage -->
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('Visit Days') }}</p>
                                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($resolution['visit_days']) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('Percentage') }}</p>
                                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($resolution['percentage'], 1) }}%</p>
                                            </div>
                                        </div>

                                        <!-- Hourly Distribution Chart -->
                                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Hourly Distribution') }}</p>
                                            <div class="h-32">
                                                <canvas
                                                    x-data="{
                                                        chart: null,
                                                        init() {
                                                            const hourlyData = {{ Js::from($resolution['hourly_distribution']) }};
                                                            this.chart = new Chart(this.$el, {
                                                                type: 'bar',
                                                                data: {
                                                                    labels: Array.from({length: 24}, (_, i) => `${String(i).padStart(2, '0')}:00`),
                                                                    datasets: [{
                                                                        data: hourlyData,
                                                                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                                                        hoverBackgroundColor: 'rgba(16, 185, 129, 0.3)',
                                                                        borderRadius: 4
                                                                    }]
                                                                },
                                                                options: {
                                                                    responsive: true,
                                                                    maintainAspectRatio: false,
                                                                    plugins: {
                                                                        legend: {
                                                                            display: false
                                                                        },
                                                                        tooltip: {
                                                                            callbacks: {
                                                                                title: function(context) {
                                                                                    const hour = parseInt(context[0].label);
                                                                                    return `${hour % 12 || 12}:00 ${hour < 12 ? 'AM' : 'PM'}`;
                                                                                },
                                                                                label: function(context) {
                                                                                    return `Visits: ${context.parsed.y}`;
                                                                                }
                                                                            }
                                                                        }
                                                                    },
                                                                    scales: {
                                                                        x: {
                                                                            display: false
                                                                        },
                                                                        y: {
                                                                            beginAtZero: true,
                                                                            grid: {
                                                                                color: 'rgba(148, 163, 184, 0.1)'
                                                                            },
                                                                            ticks: {
                                                                                color: 'rgb(148, 163, 184)'
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            });
                                                        }
                                                    }"
                                                ></canvas>
                                            </div>
                                            <div class="flex justify-between mt-1 text-xs text-slate-500">
                                                <span>{{ __('12:00 AM') }}</span>
                                                <span>{{ __('12:00 PM') }}</span>
                                                <span>{{ __('11:59 PM') }}</span>
                                            </div>
                                        </div>

                                        <!-- Meta Data -->
                                        @if(!empty($resolution['meta_data']))
                                            <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-4">{{ __('Resolution Details') }}</p>
                                                <div class="grid grid-cols-2 gap-6">
                                                    @foreach($resolution['meta_data'] as $key => $meta)
                                                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4">
                                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 capitalize mb-2">
                                                                {{ str_replace('-', ' ', $key) }}
                                                            </p>
                                                            @if(isset($meta['most_common']))
                                                                <div class="flex items-center gap-2 mb-3">
                                                                    <span class="text-sm font-medium text-slate-900 dark:text-slate-100">
                                                                        {{ $meta['most_common'] }}
                                                                    </span>
                                                                    <span class="text-xs px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded">
                                                                        {{ number_format($meta['most_common_percentage'] ?? 0, 1) }}%
                                                                    </span>
                                                                </div>
                                                                <div class="space-y-2">
                                                                    @foreach($meta['distribution'] ?? [] as $value => $count)
                                                                        <div class="flex gap-4 items-center text-xs">
                                                                            <span class="text-slate-600 dark:text-slate-400">{{ $value }}</span>
                                                                            <div class="flex-1 relative h-2 bg-slate-100 dark:bg-slate-700 rounded">
                                                                                <div class="absolute left-0 top-0 h-full bg-emerald-200 dark:bg-emerald-800 rounded"
                                                                            style="width: {{ $meta['distribution_percentages'][$value] ?? 0 }}%"></div>
                                                                            </div>
                                                                            <span class="text-slate-500 dark:text-slate-500">
                                                                                {{ $count }} ({{ number_format($meta['distribution_percentages'][$value] ?? 0, 1) }}%)
                                                                            </span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Peak Hours -->
                                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('Peak Hours') }}</p>
                                            <div class="flex gap-2">
                                                @foreach($resolution['peak_hours'] as $hour)
                                                    <span class="px-2 py-1 text-xs font-medium bg-slate-100 dark:bg-slate-700 rounded text-slate-700 dark:text-slate-300">
                                                        {{ sprintf('%02d:00', $hour) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Visitor Stats -->
                                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                                            <div class="grid grid-cols-3 gap-4">
                                                <div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                                            <x-icon name="heroicon-o-users" class="w-5 h-5 text-blue-500 dark:text-blue-400"/>
                                                        </div>
                                                        <div>
                                                            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ __('Unique Visitors') }}</p>
                                                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ number_format($resolution['unique_visitor_count']) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                                                            <x-icon name="heroicon-o-arrow-path" class="w-5 h-5 text-emerald-500 dark:text-emerald-400"/>
                                                        </div>
                                                        <div>
                                                            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ __('Total Visits') }}</p>
                                                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ number_format($resolution['total_visits']) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 dark:bg-purple-900/20">
                                                            <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 text-purple-500 dark:text-purple-400"/>
                                                        </div>
                                                        <div>
                                                            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ __('Visits/Visitor') }}</p>
                                                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ number_format($resolution['visits_per_visitor'], 1) }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <x-pagination :paginator="$resolutions" type="compact"/>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 bg-gradient-to-br from-slate-50 to-white dark:from-slate-900/90 dark:to-slate-800/90 rounded-2xl border border-dashed border-slate-200/60 dark:border-slate-700/60">
                <x-icon name="heroicon-o-device-tablet" class="w-12 h-12 text-slate-400 mb-4"/>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">{{ __('No Screen Resolution Data Available') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Start Monitoring To Collect Screen Resolution Metrics') }}</p>
            </div>
        @endif
    </div>
</x-site>

@pushOnce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endPushOnce
