@if($certificate->chain)
    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
        <div class="flex items-center gap-2 mb-2">
            <x-icon name="heroicon-o-link" class="w-5 h-5 text-gray-400" />
            <h4 class="font-bold">{{ __('Certificate Chain') }}</h4>
        </div>
        <div class="space-y-2">
            @foreach($certificate->chain as $cert)
                <div class="p-3 bg-white dark:bg-gray-800 rounded-lg">
                    <div class="flex items-center gap-2">
                        <x-icon name="{{ $cert['type'] === 'Root' ? 'heroicon-o-server' : ($cert['type'] === 'Intermediate' ? 'heroicon-o-arrows-right-left' : 'heroicon-o-document-text') }}" class="w-5 h-5 text-gray-400" />
                        <div>
                            <div class="font-medium">{{ $cert['type'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $cert['subject']['CN'] ?? $cert['subject']['O'] ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif