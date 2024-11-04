<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    @if($certificate->valid_from)
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Valid From') }}</div>
                <x-icon name="heroicon-o-calendar" class="w-5 h-5 text-gray-400" />
            </div>
            <div class="font-bold">{{ $certificate->valid_from?->format('M d, Y') }}</div>
        </div>
    @endif
    @if($certificate->valid_to)
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('Valid Until') }}</div>
                <x-icon name="heroicon-o-calendar" class="w-5 h-5 text-gray-400" />
            </div>
            <div class="font-bold">{{ $certificate->valid_to?->format('M d, Y') }}</div>
        </div>
    @endif
    @if($certificate->grade)
    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
        <div class="flex items-center justify-between mb-2">
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('SSL Grade') }}</div>
            <x-icon name="heroicon-o-academic-cap" class="w-5 h-5 text-gray-400" />
        </div>
        <div class="font-bold {{ in_array($certificate->grade, ['A+', 'A', 'A-', 'B']) ? 'text-green-600' : 'text-red-600' }}">{{ $certificate->grade }}</div>
    </div>
    @endif
    @if($certificate->ssl_version)
    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
        <div class="flex items-center justify-between mb-2">
            <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('SSL Version') }}</div>
            <x-icon name="heroicon-o-code-bracket" class="w-5 h-5 text-gray-400" />
        </div>
        <div class="font-bold">{{ $certificate->ssl_version }}</div>
    </div>
    @endif
</div>