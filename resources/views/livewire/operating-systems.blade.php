<x-site :site="$site">
    <div class="space-y-8">
        <x-breadcrumbs :breadcrumbs="[
            ['url' => route('sites.analytics.overview', ['site' => $site->id]), 'label' => __('Analytics Dashboard')],
            ['url' => route('sites.analytics.operating-systems', ['site' => $site->id]), 'label' => __('Operating Systems'), 'icon' => 'heroicon-o-computer-desktop']
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
        @if($operatingSystems->isNotEmpty())
            <div class="space-y-6">
                <div class="relative bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6 overflow-hidden" x-data="{ isOpen: true }">
                    <!-- Overview Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <!-- Total OS -->
                        <div class="bg-slate-50/50 dark:bg-slate-700/20 rounded-xl p-6">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                    <x-icon name="heroicon-o-computer-desktop" class="w-6 h-6 text-emerald-500" />
                                </div>
                                <div>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Most Common OS') }}</p>
                                    <p class="text-xl font-semibold text-emerald-600 dark:text-emerald-400">
                                        {{ $aggregates['most_common_category'] }}
                                        <span class="text-sm font-normal">({{ number_format($aggregates['most_common_percentage'] * 100, 1) }}%)</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex justify-between text-sm items-center mt-4">
                                <span class="text-slate-500 flex items-center gap-2">
                                    <x-icon name="heroicon-o-users" class="w-4 h-4 text-blue-500" />
                                    {{ __('Total OS Types') }}:
                                </span>
                                <span class="font-medium text-blue-600 dark:text-blue-400">{{ number_format($aggregates['total_categories']) }}</span>
                            </div>
                            <div class="flex justify-between text-sm items-center mt-2">
                                <span class="text-slate-500 flex items-center gap-2">
                                    <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 text-purple-500" />
                                    {{ __('Diversity Index') }}:
                                </span>
                                <span class="font-medium text-purple-600 dark:text-purple-400">{{ number_format($aggregates['category_diversity_index'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Engagement Metrics -->
                        <div class="bg-slate-50/50 dark:bg-slate-700/20 rounded-xl p-6">
                            <h3 class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2 mb-4">
                                <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 text-indigo-500" />
                                {{ __('Visit Statistics') }}
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm items-center">
                                    <span class="text-slate-500 flex items-center gap-2">
                                        <x-icon name="heroicon-o-arrow-trending-up" class="w-4 h-4 text-sky-500" />
                                        {{ __('Avg Visits/OS') }}:
                                    </span>
                                    <span class="font-medium text-sky-600 dark:text-sky-400">{{ number_format($aggregates['average_visits_per_category'], 1) }}</span>
                                </div>
                                <div class="flex justify-between text-sm items-center">
                                    <span class="text-slate-500 flex items-center gap-2">
                                        <x-icon name="heroicon-o-arrow-uturn-left" class="w-4 h-4 text-teal-500" />
                                        {{ __('Peak Activity') }}:
                                    </span>
                                    <span class="font-medium text-teal-600 dark:text-teal-400">{{ number_format($aggregates['peak_activity_percentage'], 1) }}%</span>
                                </div>
                                <div class="flex justify-between text-sm items-center">
                                    <span class="text-slate-500 flex items-center gap-2">
                                        <x-icon name="heroicon-o-star" class="w-4 h-4 text-amber-500" />
                                        {{ __('Total Visits') }}:
                                    </span>
                                    <span class="font-medium text-amber-600 dark:text-amber-400">{{ number_format($aggregates['total_visits']) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Visit Patterns -->
                        <div class="bg-slate-50/50 dark:bg-slate-700/20 rounded-xl p-6">
                            <h3 class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2 mb-4">
                                <x-icon name="heroicon-o-chart-pie" class="w-5 h-5 text-rose-500" />
                                {{ __('Visit Patterns') }}
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between text-sm items-center">
                                    <span class="text-slate-500 flex items-center gap-2">
                                        <x-icon name="heroicon-o-arrow-trending-up" class="w-4 h-4 text-red-500" />
                                        {{ __('Highest Visits/User') }}:
                                    </span>
                                    <span class="font-medium text-red-600 dark:text-red-400">{{ number_format($aggregates['highest_visits_per_visitor'], 1) }}</span>
                                </div>
                                <div class="flex justify-between text-sm items-center">
                                    <span class="text-slate-500 flex items-center gap-2">
                                        <x-icon name="heroicon-o-user-group" class="w-4 h-4 text-orange-500" />
                                        {{ __('Multi-Visit OS') }}:
                                    </span>
                                    <span class="font-medium text-orange-600 dark:text-orange-400">{{ number_format($aggregates['categories_with_multiple_visits']) }}</span>
                                </div>
                                <div class="flex justify-between text-sm items-center">
                                    <span class="text-slate-500 flex items-center gap-2">
                                        <x-icon name="heroicon-o-clock" class="w-4 h-4 text-pink-500" />
                                        {{ __('Median Visits') }}:
                                    </span>
                                    <span class="font-medium text-pink-600 dark:text-pink-400">{{ number_format($aggregates['median_visits_per_visitor'], 1) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Operating Systems List -->
                    <div class="grid grid-cols-1 gap-6">
                        @foreach($operatingSystems as $os)
                            <div class="relative bg-slate-50/50 dark:bg-slate-700/20 rounded-xl p-6" x-data="{ showDetails: false }">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ asset('vendor/analytics/icons/os/'.formatOperatingSystem($os['value']).'.svg') }}" class="w-6 h-6 text-slate-400" />
                                        <span class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $os['value'] }}</span>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-800/30 dark:text-emerald-200">
                                        {{ number_format($os['unique_visitors']) }} {{ __('Users') }}
                                    </span>
                                </div>

                                <!-- Basic Stats -->
                                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                    <div>
                                        <p class="text-slate-500 dark:text-slate-400">{{ __('First Seen') }}</p>
                                        <p class="font-medium text-slate-700 dark:text-slate-300">{{ Carbon\Carbon::parse($os['first_seen'])->format('M j, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-slate-500 dark:text-slate-400">{{ __('Last Seen') }}</p>
                                        <p class="font-medium text-slate-700 dark:text-slate-300">{{ Carbon\Carbon::parse($os['last_seen'])->format('M j, Y') }}</p>
                                    </div>
                                </div>

                                <!-- Show More Button -->
                                <button
                                    @click="showDetails = !showDetails"
                                    class="w-full py-2 px-4 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700/50 rounded-lg transition-colors flex items-center justify-center gap-2"
                                >
                                    <span x-text="showDetails ? '{{ __('Show Less') }}' : '{{ __('Show More') }}'"></span>
                                    <x-icon
                                        :name="'heroicon-o-chevron-down'"
                                        class="w-4 h-4 transition-transform"
                                        ::class="showDetails ? 'rotate-180' : ''"
                                    />
                                </button>

                                <!-- Detailed Content -->
                                <div x-show="showDetails" x-collapse class="mt-4 space-y-4">
                                    <!-- Meta Data -->
                                    <div class="grid grid-cols-2 gap-6">
                                        @foreach(['platform', 'device-type', 'language', 'vendor'] as $key)
                                            @if(!empty($os['meta_data'][$key]))
                                                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4">
                                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 capitalize mb-2">
                                                        {{ str_replace('-', ' ', $key) }}
                                                    </p>
                                                    <div class="space-y-2">
                                                        @foreach($os['meta_data'][$key]['distribution'] ?? [] as $value => $count)
                                                            <div class="flex gap-4 items-center text-xs">
                                                                <span class="text-slate-600 dark:text-slate-400">{{ $value }}</span>
                                                                <div class="flex-1 relative h-2 bg-slate-100 dark:bg-slate-700 rounded">
                                                                    <div class="absolute left-0 top-0 h-full bg-emerald-200 dark:bg-emerald-800 rounded"
                                                                         style="width: {{ $os['meta_data'][$key]['distribution_percentages'][$value] ?? 0 }}%"></div>
                                                                </div>
                                                                <span class="text-slate-500 dark:text-slate-500">
                                                                    {{ $count }} ({{ number_format($os['meta_data'][$key]['distribution_percentages'][$value] ?? 0, 1) }}%)
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <x-pagination :paginator="$operatingSystems" type="compact"/>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 bg-gradient-to-br from-slate-50 to-white dark:from-slate-900/90 dark:to-slate-800/90 rounded-2xl border border-dashed border-slate-200/60 dark:border-slate-700/60">
                <x-icon name="heroicon-o-computer-desktop" class="w-12 h-12 text-slate-400 mb-4"/>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">{{ __('No Operating System Data Available') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Start Monitoring To Collect Operating System Metrics') }}</p>
            </div>
        @endif
    </div>
</x-site>
