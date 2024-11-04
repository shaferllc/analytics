<div class="bg-gradient-to-r from-emerald-50/50 to-teal-50/50 dark:from-gray-800/50 dark:to-gray-700/50 rounded-xl border border-emerald-100 dark:border-gray-700 shadow-lg hover:shadow-[0_20px_60px_-15px_rgba(16,185,129,0.5)] transition-all duration-500 transform hover:-translate-y-2 hover:scale-[1.02] z-10">
    <div class="p-8 relative overflow-hidden">
        <!-- Retro grid background with animation -->
        <div class="absolute inset-0 bg-[linear-gradient(0deg,rgba(16,185,129,0.1)_1px,transparent_1px),linear-gradient(90deg,rgba(16,185,129,0.1)_1px,transparent_1px)] bg-[size:15px_15px] opacity-30 animate-grid-scroll"></div>
        
        <!-- Glowing orbs in background -->
        <div class="absolute -top-20 -left-20 w-40 h-40 bg-emerald-500/30 dark:bg-emerald-600/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-teal-500/30 dark:bg-teal-600/30 rounded-full blur-3xl animate-pulse delay-1000"></div>
        
        <div class="flex items-center justify-between relative">
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-gradient-to-br from-pink-500 via-purple-500 to-indigo-500 rounded-2xl blur-lg group-hover:blur-xl transition-all duration-300"></div>
                        <div class="relative flex items-center justify-center bg-white/10 dark:bg-gray-900/50 backdrop-blur-md rounded-2xl w-16 h-16 group-hover:scale-110 transition-all duration-500">
                            <div class="absolute inset-0 bg-gradient-to-br from-pink-500/20 via-purple-500/20 to-indigo-500/20 rounded-2xl animate-pulse"></div>
                            <x-icon name="heroicon-o-document-text" class="w-8 h-8 text-white transform group-hover:rotate-12 transition-transform duration-500" />
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <h3 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400 animate-text-shimmer hover:scale-110 transition-transform duration-300">{{ __('PAGEVIEWS') }}</h3>
                        <div class="group relative flex-shrink-0">
                            <x-icon name="heroicon-o-information-circle" 
                                class="w-6 h-6 text-emerald-500 dark:text-emerald-400 group-hover:text-teal-400 transition-colors cursor-help animate-spin-slow"
                                x-data="{}"
                                x-tooltip.raw="{{ __('A pageview represents a page load of your website.') }}" />
                            <div class="absolute hidden group-hover:block bg-white/95 dark:bg-gray-800/95 text-emerald-600 dark:text-emerald-400 text-sm rounded-xl py-3 px-4 -mt-2 mx-auto left-1/2 transform -translate-x-1/2 w-72 z-50 pointer-events-none border-2 border-emerald-400 dark:border-emerald-600 shadow-xl backdrop-blur-sm">
                                {{ __('A pageview represents a page load of your website.') }}
                                <div class="absolute w-3 h-3 bg-white dark:bg-gray-800 rotate-45 -top-1.5 left-1/2 transform -translate-x-1/2 border-l-2 border-t-2 border-emerald-400 dark:border-emerald-600"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                include('analytics::livewire.partials.growth', ['growthCurrent' => $totalPageviews, 'growthPrevious' => $pageviewsOld, 'type' => 'pageviews'])
            </div>
            <div class="text-7xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400 animate-text-shimmer tracking-tighter hover:scale-110 transition-transform duration-300" style="text-shadow: 4px 4px 8px rgba(16,185,129,0.5);" id="pageviews-count">
                {{ array_sum($pageviewCounts) }}
            </div>
        </div>
    </div>
</div>