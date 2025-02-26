<x-site :site="$site">
    <div class="space-y-8">
        <x-breadcrumbs :breadcrumbs="[
            ['url' => route('sites.analytics.overview', ['site' => $site->id]), 'label' => __('Analytics Dashboard')],
            ['url' => route('sites.analytics.campaigns', ['site' => $site->id]), 'label' => __('Campaigns'), 'icon' => 'fas-bullhorn']
        ]" />

        <div class="flex justify-end gap-4">
            <x-ts-dropdown position="bottom-end">
                <x-slot:action>
                    <x-ts-button x-on:click="show = !show" sm>{{ __('Time Range') }}</x-ts-button>
                </x-slot:action>
                <x-ts-dropdown.items wire:click="setTimeRange('today')" :active="$daterange === 'today'">{{ __('Today') }}</x-ts-dropdown.items>
                <x-ts-dropdown.items wire:click="setTimeRange('7d')" :active="$daterange === '7d'">{{ __('Last 7 Days') }}</x-ts-dropdown.items>
                <x-ts-dropdown.items wire:click="setTimeRange('30d')" :active="$daterange === '30d'">{{ __('Last 30 Days') }}</x-ts-dropdown.items>
                <x-ts-dropdown.items wire:click="setTimeRange('90d')" :active="$daterange === '90d'">{{ __('Last 90 Days') }}</x-ts-dropdown.items>
            </x-ts-dropdown>

            <x-ts-button wire:click="exportData" sm>
                <x-icon name="heroicon-o-arrow-down-tray" class="w-4 h-4 mr-2" />
                {{ __('Export') }}
            </x-ts-button>
        </div>

        <div class="grid grid-cols-1 gap-4">
            <div class="relative bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Basic Stats -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-50 dark:bg-emerald-800/20 rounded-lg">
                                <x-icon name="fas-bullhorn" class="w-6 h-6 text-emerald-500" />
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Total Campaigns') }}</p>
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
                                {{ __('Avg Visits/Campaign') }}:
                            </span>
                            <span class="font-medium text-sky-600 dark:text-sky-400">{{ number_format($aggregates['average_visits_per_visitor'], 1) }}</span>
                        </div>
                        <div class="flex justify-between text-sm items-center">
                            <span class="text-slate-500 flex items-center gap-2">
                                <x-icon name="heroicon-o-arrow-uturn-left" class="w-4 h-4 text-teal-500" />
                                {{ __('Return Rate') }}:
                            </span>
                            <span class="font-medium text-teal-600 dark:text-teal-400">{{ number_format($aggregates['visitor_retention_rate'], 1) }}%</span>
                        </div>
                    </div>

                    <!-- Visit Distribution -->
                    <div class="space-y-2">
                        <h3 class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
                            <x-icon name="heroicon-o-chart-pie" class="w-5 h-5 text-rose-500" />
                            {{ __('Campaign Patterns') }}
                        </h3>
                        <div class="flex justify-between text-sm items-center">
                            <span class="text-slate-500 flex items-center gap-2">
                                <x-icon name="heroicon-o-user" class="w-4 h-4 text-red-500" />
                                {{ __('Single Visit Campaigns') }}:
                            </span>
                            <span class="font-medium text-red-600 dark:text-red-400">{{ number_format($aggregates['categories_with_single_visit']) }}</span>
                        </div>
                        <div class="flex justify-between text-sm items-center">
                            <span class="text-slate-500 flex items-center gap-2">
                                <x-icon name="heroicon-o-users" class="w-4 h-4 text-orange-500" />
                                {{ __('Multiple Visit Campaigns') }}:
                            </span>
                            <span class="font-medium text-orange-600 dark:text-orange-400">{{ number_format($aggregates['categories_with_multiple_visits']) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-loading />
        @if($campaigns->isNotEmpty())
            <div class="space-y-6">
                <div class="relative bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6 overflow-hidden" x-data="{ isOpen: true }">
                    <div class="cursor-pointer" @click="isOpen = !isOpen">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="space-y-2 flex-initial">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                        <x-icon name="fas-bullhorn" class="w-6 h-6 text-emerald-500" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
                                            {{ __('Marketing Campaigns') }}
                                        </h2>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            {{ __('Detailed Insights Into Campaign Performance') }}
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            @foreach($campaigns as $campaign)
                                <div class="relative bg-slate-50/50 dark:bg-slate-700/20 rounded-xl p-6" x-data="{ showDetails: false }">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <x-icon name="fas-bullhorn" class="w-6 h-6 text-slate-400" />
                                            <span class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $campaign['value'] }}</span>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-800/30 dark:text-emerald-200">
                                            {{ number_format($campaign['unique_visitors']) }} {{ __('Users') }}
                                        </span>
                                    </div>

                                    <!-- Basic Stats - Always Visible -->
                                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-400">{{ __('First Seen') }}</p>
                                            <p class="font-medium text-slate-700 dark:text-slate-300">{{ Carbon\Carbon::parse($campaign['first_seen'])->format('M j, Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-400">{{ __('Last Seen') }}</p>
                                            <p class="font-medium text-slate-700 dark:text-slate-300">{{ Carbon\Carbon::parse($campaign['last_seen'])->format('M j, Y') }}</p>
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
                                        <!-- Visit Days and Percentage -->
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('Active Days') }}</p>
                                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($campaign['visit_days']) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('Traffic Share') }}</p>
                                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($campaign['percentage'], 1) }}%</p>
                                            </div>
                                        </div>

                                        <!-- Campaign Meta Data -->
                                        @if(!empty($campaign['meta_data']))
                                            <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-4">{{ __('Campaign Details') }}</p>
                                                <div class="grid grid-cols-2 gap-6">
                                                    @foreach($campaign['meta_data'] as $key => $meta)
                                                        <div>
                                                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __(Str::title(str_replace('-', ' ', $key))) }}</p>
                                                            <p class="font-medium text-slate-700 dark:text-slate-300">{{ $meta }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Campaign Performance -->
                                        <div class="border-t border-slate-200 dark:border-slate-700 pt-4">
                                            <div class="grid grid-cols-3 gap-4">
                                                <div>
                                                    <div class="flex items-start space-x-3">
                                                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                                            <x-icon name="heroicon-o-users" class="w-5 h-5 text-blue-500 dark:text-blue-400"/>
                                                        </div>
                                                        <div>
                                                            <p class="text-slate-500 dark:text-slate-400 text-xs">{{ __('Unique Visitors') }}</p>
                                                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ number_format($campaign['unique_visitor_count']) }}</p>
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
                                                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ number_format($campaign['total_visits']) }}</p>
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
                                                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">{{ number_format($campaign['visits_per_visitor'], 1) }}</p>
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
                        <x-pagination :paginator="$campaigns" type="compact"/>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 bg-gradient-to-br from-slate-50 to-white dark:from-slate-900/90 dark:to-slate-800/90 rounded-2xl border border-dashed border-slate-200/60 dark:border-slate-700/60">
                <x-icon name="fas-bullhorn" class="w-12 h-12 text-slate-400 mb-4"/>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">{{ __('No Campaign Data Available') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Start Tracking To Collect Campaign Metrics') }}</p>
            </div>
        @endif
    </div>
</x-site>

@pushOnce('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endPushOnce
