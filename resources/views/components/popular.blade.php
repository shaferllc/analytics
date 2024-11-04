@props([
    'first',
    'last',
    'firstIcon' => null,
    'lastIcon' => null,
    'website' => null,
])

<div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-5">
    <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-xl p-5 shadow-lg">
        <h3 class="text-lg font-bold text-white mb-3">{{ __('Most Popular') }}</h3>
        <div class="flex flex-col space-y-3">
            @if(isset($first->value))
                <div class="flex items-center space-x-3">
                    <div class="bg-white p-2 rounded-full">{!! $firstIcon !!}</div>
                    <span class="text-base font-bold text-white break-all">
                        {{ Str::limit($first->value ? Str::ucfirst($first->value) : __('Unknown'), 30) }}
                    </span>
                </div>
                <div class="text-xl font-bold text-white">
                    {{ isset($first->count) ? number_format($first->count, 0, __('.'), __(',')) : '—' }} {{ __('views') }}
                </div>
                @if($website && $first->value)
                    <a href="http://{{ $website->domain . $first->value }}" target="_blank" rel="noopener noreferrer" class="text-sm text-white hover:text-green-200 transition duration-300 flex items-center">
                        <span class="underline">{{ __('View page') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                            <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                        </svg>
                    </a>
                @endif
            @else
                <span class="text-base font-bold text-white">{{ __('No data available') }}</span>
            @endif
        </div>
    </div>

    <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-xl p-5 shadow-lg">
        <h3 class="text-lg font-bold text-white mb-3">{{ __('Least Popular') }}</h3>
        <div class="flex flex-col space-y-3">
            @if(isset($last->value))
                <div class="flex items-center space-x-3">
                    <div class="bg-white p-2 rounded-full">{!! $lastIcon !!}</div>
                    <span class="text-base font-bold text-white break-all">
                        {{ Str::limit($last->value ? Str::ucfirst($last->value) : __('Unknown'), 30) }}
                    </span>
                </div>
                <div class="text-xl font-bold text-white">
                    {{ isset($last->count) ? number_format($last->count, 0, __('.'), __(',')) : '—' }} {{ __('views') }}
                </div>
                @if($website && $last->value)
                    <a href="http://{{ $website->domain . $last->value }}" target="_blank" rel="noopener noreferrer" class="text-sm text-white hover:text-red-200 transition duration-300 flex items-center">
                        <span class="underline">{{ __('View page') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                            <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                        </svg>
                    </a>
                @endif
            @else
                <span class="text-base font-bold text-white">{{ __('No data available') }}</span>
            @endif
        </div>
    </div>
</div>
