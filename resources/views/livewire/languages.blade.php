<x-site :site="$site">
    <div class="space-y-8">
        <x-breadcrumbs :breadcrumbs="[
            ['url' => route('sites.analytics.overview', ['site' => $site->id]), 'label' => __('Analytics Dashboard')],
            ['url' => route('sites.analytics.languages', ['site' => $site->id]), 'label' => __('Languages'), 'icon' => 'heroicon-o-language']
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

        <x-loading />
        @if($languages->isNotEmpty())
            <div class="space-y-6">
                <div class="relative bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6 overflow-hidden" x-data="{ isOpen: true }">
                    <div class="cursor-pointer" @click="isOpen = !isOpen">
                        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                            <div class="space-y-2 flex-initial">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                                        <x-icon name="heroicon-o-language" class="w-6 h-6 text-emerald-500" />
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
                                            {{ __('Languages') }}
                                        </h2>
                                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                            {{ __('Visitor Language Distribution') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 p-2 hover:bg-slate-100/50 dark:hover:bg-slate-700/20 rounded-lg transition-colors">
                                <div>
                                    <template x-if="isOpen">
                                        <x-icon name="heroicon-o-chevron-up" class="w-6 h-6 text-slate-400 bg-slate-100/50 dark:bg-slate-500/20 rounded-lg p-1" />
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
                            @foreach($languages as $language)
                                <div class="relative bg-slate-50/50 dark:bg-slate-700/20 rounded-xl p-6" x-data="{ showDetails: false }">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <x-icon name="heroicon-o-language" class="w-6 h-6 text-slate-400" />
                                            <span class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $language['value'] }}</span>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-800/30 dark:text-emerald-200">
                                            {{ number_format($language['unique_visitors']) }} {{ __('Users') }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-400">{{ __('Total Visits') }}</p>
                                            <p class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($language['total_visits']) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-slate-500 dark:text-slate-400">{{ __('Percentage') }}</p>
                                            <p class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($language['percentage'], 1) }}%</p>
                                        </div>
                                    </div>

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

                                    <div x-show="showDetails" x-collapse class="mt-4 space-y-4">
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('First Seen') }}</p>
                                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($language['first_seen'])->format('M j, Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('Last Seen') }}</p>
                                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ \Carbon\Carbon::parse($language['last_seen'])->format('M j, Y') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('Visits/Visitor') }}</p>
                                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($language['visits_per_visitor'], 1) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('Visit Days') }}</p>
                                                <p class="font-medium text-slate-700 dark:text-slate-300">{{ number_format($language['visit_days']) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <x-pagination :paginator="$languages" type="compact"/>
                    </div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-20 bg-gradient-to-br from-slate-50 to-white dark:from-slate-900/90 dark:to-slate-800/90 rounded-2xl border border-dashed border-slate-200/60 dark:border-slate-700/60">
                <x-icon name="heroicon-o-language" class="w-12 h-12 text-slate-400 mb-4"/>
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-2">{{ __('No Language Data Available') }}</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Start Monitoring To Collect Language Metrics') }}</p>
            </div>
        @endif
    </div>
</x-site>

