@props([
    'view',
    'class' => '',
    'gradient' => true,
    'border' => true,
    'shadow' => true,
    'padding' => 'p-6',
])

<div x-show="view === '{{ $view }}'"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform scale-100"
     x-transition:leave-end="opacity-0 transform scale-95"
     x-bind:class="{ 'hidden': !view || view !== '{{ $view }}' }"
     {{ $attributes->merge(['class' => 'relative']) }}
>
    @if($gradient)
        <div class="absolute inset-0 bg-gradient-to-br from-slate-50/50 to-slate-100/20 dark:from-slate-900/10 dark:to-slate-950/10 rounded-lg"></div>
    @endif

    <div class="relative {{ $class }} {{ $padding }}
        @if($border) border border-slate-200/60 dark:border-slate-700/60 @endif
        @if($shadow) shadow-lg @endif
        rounded-2xl bg-white/90 dark:bg-slate-800/90">
        {{ $slot }}
    </div>
</div>
