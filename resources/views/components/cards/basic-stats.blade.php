@props([
    'stat' => null,
    'iconColor' => 'blue',
    'showTooltip' => true,
    'compact' => false,
])

@php
    $iconColor = match($iconColor) {
        'blue' => 'hover:text-blue-500',
        'red' => 'hover:text-red-500',
        'green' => 'hover:text-green-500',
        'yellow' => 'hover:text-yellow-500',
        'purple' => 'hover:text-purple-500',
        default => 'hover:text-blue-500',
    };

    $textColors = match($iconColor) {
        'blue' => 'text-blue-500',
        'red' => 'text-red-500',
        'green' => 'text-green-500',
        'yellow' => 'text-yellow-500',
        'purple' => 'text-purple-500',
        default => 'text-blue-500',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white/50 dark:bg-slate-800/50 rounded-lg p-3 relative transition-all hover:shadow-sm']) }}>
    <!-- Tooltip -->
    @if($showTooltip && isset($stat['tooltip']))
        <div class="absolute top-3 right-3">
            <x-popover placement="bottom-end">
                <x-slot:trigger>
                    <x-icon name="fas-info-circle" class="w-4 h-4 text-slate-400 {{ $iconColor }} cursor-pointer transition-colors duration-200" />
                </x-slot:trigger>
                <div class="text-sm max-w-xs p-2">
                    {!! $stat['tooltip'] !!}
                </div>
            </x-popover>
        </div>
    @endif

    <!-- Content -->
    <div class="space-y-1">
        <!-- Label and Icon -->
        <div class="flex items-center gap-2">
            <x-icon
                :name="$stat['icon']"
                class="w-4 h-4 {{ $textColors }}"
                aria-hidden="true"
            />
            <span class="text-xs text-slate-500 dark:text-slate-400">
                {{ $stat['label'] }}
            </span>
        </div>

        <!-- Value -->
        <p class="font-medium text-slate-700 dark:text-slate-300 text-sm @if($compact) truncate @endif">
            {{ $stat['value'] }}
            @isset($stat['unit'])
                <span class="text-xs text-slate-400 ml-1">{{ $stat['unit'] }}</span>
            @endisset
        </p>

        <!-- Optional Trend Indicator -->
        @isset($stat['trend'])
            <div class="flex items-center gap-1 text-xs mt-1">
                <x-icon
                    :name="$stat['trend']['direction'] === 'up' ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down'"
                    class="w-3 h-3 {{ $stat['trend']['direction'] === 'up' ? 'text-green-500' : 'text-red-500' }}"
                />
                <span class="{{ $stat['trend']['direction'] === 'up' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $stat['trend']['value'] }}
                </span>
            </div>
        @endisset
    </div>
</div>
