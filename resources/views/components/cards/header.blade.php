@props([
    'icon' => null,
    'title' => null,
    'description' => null,
    'visitors' => null,
    'color' => 'blue',
    'visitors_label' => null,
])
@php
    $bgColors = match($color) {
        'blue' => 'bg-blue-50 dark:bg-blue-900/20',
        'red' => 'bg-red-50 dark:bg-red-900/20',
        'green' => 'bg-green-50 dark:bg-green-900/20',
        'yellow' => 'bg-yellow-50 dark:bg-yellow-900/20',
        'purple' => 'bg-purple-50 dark:bg-purple-900/20',
    };

    $textColors = match($color) {
        'blue' => 'text-blue-500',
        'red' => 'text-red-500',
        'green' => 'text-green-500',
        'yellow' => 'text-yellow-500',
        'purple' => 'text-purple-500',
    };
@endphp
<div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
    <div class="flex items-start gap-4 flex-grow">
        <div class="p-3 {{ $bgColors }} rounded-lg">
            <x-icon name="{{ $icon }}" class="w-8 h-8 {{ $textColors }}" />
        </div>
        <div class="space-y-1">
            <div class="flex items-center gap-3">
                <code class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                    {{ $title }}
                </code>
            </div>
            <div class="flex items-center gap-3">
                <p class="text-sm text-slate-500 dark:text-slate-400 font-mono break-all">
                    {{ $description }}
                </p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-800/30 dark:text-emerald-200">
                    {{ number_format($visitors) }} {{ $visitors_label }}
                </span>
            </div>
        </div>
    </div>
</div>
