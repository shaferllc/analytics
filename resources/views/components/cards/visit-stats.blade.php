@props([
    'stat' => null,
    'showTooltip' => true,
])

@php
    $textColors = match($stat['color']) {
        'slate' => 'text-slate-500 hover:text-slate-600',
        'blue' => 'text-blue-500 hover:text-blue-600',
        'red' => 'text-red-500 hover:text-red-600',
        'green' => 'text-green-500 hover:text-green-600',
        'yellow' => 'text-yellow-500 hover:text-yellow-600',
        'purple' => 'text-purple-500 hover:text-purple-600',
        'emerald' => 'text-emerald-500 hover:text-emerald-600',
        default => 'text-blue-500 hover:text-blue-600',
    };

    $bgColors = match($stat['color']) {
        'slate' => 'bg-slate-50/50 dark:bg-slate-700/20',
        'blue' => 'bg-blue-50/50 dark:bg-blue-900/10',
        'red' => 'bg-red-50/50 dark:bg-red-900/10',
        'green' => 'bg-green-50/50 dark:bg-green-900/10',
        'yellow' => 'bg-yellow-50/50 dark:bg-yellow-900/10',
        'emerald' => 'bg-emerald-50/50 dark:bg-emerald-900/10',
        'purple' => 'bg-purple-50/50 dark:bg-purple-900/10',
        default => 'bg-blue-50/50 dark:bg-blue-900/10',
    };

    $borderColors = match($stat['color']) {
        'slate' => 'border-slate-200/60 dark:border-slate-700/60',
        'blue' => 'border-blue-200/60 dark:border-blue-700/60',
        'red' => 'border-red-200/60 dark:border-red-700/60',
        'green' => 'border-green-200/60 dark:border-green-700/60',
        'emerald' => 'border-emerald-200/60 dark:border-emerald-700/60',
        'purple' => 'border-purple-200/60 dark:border-purple-700/60',
        default => 'border-blue-200/60 dark:border-blue-700/60',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white/50 dark:bg-slate-800/50 rounded-xl p-3 relative transition-all hover:shadow-sm border border-slate-200/60 dark:border-slate-700/60']) }}>
    <!-- Tooltip -->
    @if($showTooltip && isset($stat['tooltip']))
        <div class="absolute top-2 right-2">
            <x-popover placement="bottom-end">
                <x-slot:trigger>
                    <x-icon name="fas-info-circle" class="w-4 h-4 {{ $textColors }} cursor-pointer transition-colors duration-200" />
                </x-slot:trigger>
                <div class="text-sm max-w-xs p-2">
                    {!! $stat['tooltip'] !!}
                </div>
            </x-popover>
        </div>
    @endif

    <!-- Content -->
    <div class="flex items-center gap-3">
        <!-- Icon Container -->
        <div class="shrink-0 p-2.5 {{ $bgColors }} rounded-lg border border-{{ $borderColors }}">
            <x-icon :name="$stat['icon']" class="w-5 h-5 {{ $textColors }}" />
        </div>

        <!-- Text Content -->
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 truncate mb-0.5">
                {{ $stat['label'] }}
            </p>
            <p class="text-lg font-semibold {{ $textColors }} truncate">
                {{ $stat['value'] }}
            </p>
        </div>
    </div>
</div>
