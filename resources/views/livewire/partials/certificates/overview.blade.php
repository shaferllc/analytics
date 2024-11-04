<div class="bg-white/80 dark:bg-gray-800/90 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-gray-200/50 dark:border-gray-700/50 mb-8">
    <div class="flex items-center gap-4 mb-6">
        <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg shadow-blue-500/20">
            <x-icon name="heroicon-o-shield-check" class="w-6 h-6 text-white" />
        </div>
        <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">{{ __('Overview') }}</h2>
    </div>
    
    <div class="space-y-4">
        <div class="p-4 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $website->certificates->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Total Domains') }}</div>
                </div>
                <x-icon name="heroicon-o-globe-alt" class="w-8 h-8 text-gray-400" />
            </div>
        </div>

        <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $website->certificates->where('was_valid', true)->count() }}</div>
                    <div class="text-sm text-blue-600 dark:text-blue-400">{{ __('Total Certificates') }}</div>
                </div>
                <x-icon name="heroicon-o-document-duplicate" class="w-8 h-8 text-blue-500" />
            </div>
        </div>
        
        <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $website->certificates->where('was_valid', true)->count() }}</div>
                    <div class="text-sm text-green-600 dark:text-green-400">{{ __('Active & Valid') }}</div>
                </div>
                <x-icon name="heroicon-o-check-circle" class="w-8 h-8 text-green-500" />
            </div>
        </div>
        
        <div class="p-4 bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $website->certificates->filter(fn($cert) => !$cert->was_valid)->count() }}</div>
                    <div class="text-sm text-red-600 dark:text-red-400">{{ __('Without Certificate') }}</div>
                </div>
                <x-icon name="heroicon-o-exclamation-circle" class="w-8 h-8 text-red-500" />
            </div>
        </div>

        <div class="p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/30 dark:to-yellow-800/30 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $website->certificates->filter(fn($cert) => $cert->hasWeakGrade())->count() }}</div>
                    <div class="text-sm text-yellow-600 dark:text-yellow-400">{{ __('Weak Grade') }}</div>
                </div>
                <x-icon name="heroicon-o-exclamation-triangle" class="w-8 h-8 text-yellow-500" />
            </div>
        </div>

        <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $website->certificates->where('valid_to', '>', now())->where('valid_to', '<=', now()->addDays(30))->count() }}</div>
                    <div class="text-sm text-purple-600 dark:text-purple-400">{{ __('Expiring Soon') }}</div>
                </div>
                <x-icon name="heroicon-o-clock" class="w-8 h-8 text-purple-500" />
            </div>
        </div>
    </div>
</div>