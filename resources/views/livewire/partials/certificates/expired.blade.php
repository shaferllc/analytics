<div class="p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg text-sm">
    <div class="flex items-center gap-1">
        <x-icon name="heroicon-o-clock" class="w-3 h-3 text-gray-400" />
        <span class="font-medium">{{ __('Expired') }}:</span>
        <span class="text-gray-600 dark:text-gray-400">{{ $certificate->valid_to?->format('M d, Y') ?? __('Unknown') }}</span>
    </div>
</div>