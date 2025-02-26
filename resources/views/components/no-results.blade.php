<div class="bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-8">
    <div class="flex flex-col items-center space-y-6 text-center">
        <div class="p-4 rounded-full bg-slate-100/50 dark:bg-slate-700/20">
            <x-icon name="heroicon-o-light-bulb" class="w-12 h-12 text-amber-400" />
        </div>

        <div class="space-y-4">
            <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100">{{ __('No Data Available') }}</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('We haven\'t collected any data for this time period yet.') }}</p>
        </div>

        <div class="w-full max-w-md space-y-4">
            <div class="bg-slate-50/50 dark:bg-slate-700/20 rounded-lg p-4">
                <h4 class="text-sm font-medium text-slate-900 dark:text-slate-100 mb-3">{{ __('Suggestions') }}</h4>
                <ul class="text-sm text-slate-600 dark:text-slate-300 space-y-2">
                    <li class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/50 dark:hover:bg-slate-700/30 transition-colors">
                        <x-icon name="heroicon-o-calendar" class="w-4 h-4 text-slate-400" />
                        <span>{{ __('Adjust your date range') }}</span>
                    </li>
                    <li class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/50 dark:hover:bg-slate-700/30 transition-colors">
                        <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 text-slate-400" />
                        <span>{{ __('Check back later') }}</span>
                    </li>
                    <li class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/50 dark:hover:bg-slate-700/30 transition-colors">
                        <x-icon name="heroicon-o-code-bracket" class="w-4 h-4 text-slate-400" />
                        <span>{{ __('Verify tracking code installation') }}</span>
                    </li>
                </ul>
            </div>

            <div class="text-sm text-slate-500 dark:text-slate-400">
                {{ __('Data collection in progress...') }}
            </div>
        </div>
    </div>
</div>
