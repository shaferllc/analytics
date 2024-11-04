<div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
    <div class="flex items-center gap-2 mb-4">
        <x-icon name="heroicon-o-clock" class="w-5 h-5 text-gray-400" />
        <h4 class="font-bold">{{ __('Certificate Timeline') }}</h4>
    </div>
    <div class="relative">
        <div class="space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-green-500 shadow-lg shadow-green-500/50 flex items-center justify-center">
                    <x-icon name="heroicon-o-check" class="w-4 h-4 text-white drop-shadow" />
                </div>
                <div>
                    <div class="font-medium">{{ __('Issued') }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $certificate->valid_from?->format('M d, Y H:i') ?? __('Unknown') }}
                    </div>
                </div>
            </div>
            
            @if($certificate->last_checked_at)
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 shadow-lg shadow-blue-500/50 flex items-center justify-center">
                    <x-icon name="heroicon-o-clock" class="w-4 h-4 text-white drop-shadow" />
                </div>
                <div>
                    <div class="font-medium text-blue-600 dark:text-blue-400">{{ __('Last Verified') }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $certificate->last_checked_at->format('M d, Y H:i') }}
                    </div>
                </div>
            </div>
            @endif
            
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-red-400 to-red-600 shadow-lg shadow-red-500/50 flex items-center justify-center">
                    <x-icon name="heroicon-o-x-mark" class="w-4 h-4 text-white drop-shadow" />
                </div>
                <div>
                    <div class="font-medium text-red-600 dark:text-red-400">{{ __('Expires') }}</div>
                    <div class="text-sm text-red-600 dark:text-red-400">
                        {{ $certificate->valid_to?->format('M d, Y H:i') ?? __('Unknown') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>