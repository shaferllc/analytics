@if($certificate->additional_domains)
    <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
        <div class="flex items-center gap-2 mb-2">
            <x-icon name="heroicon-o-globe-alt" class="w-5 h-5 text-gray-400" />
            <h4 class="font-bold">{{ __('Additional Domains') }}</h4>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($certificate->additional_domains as $domain)
                <span class="px-3 py-1 bg-gray-200 dark:bg-gray-600 rounded-full text-sm">{{ $domain }}</span>
            @endforeach
        </div>
    </div>
@endif