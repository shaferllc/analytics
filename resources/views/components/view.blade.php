@props([
    'view',
    'color' => 'rose',
    'class' => '',
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

    <div class="absolute inset-0 bg-gradient-to-br from-{{ $color }}-900/10 to-{{ $color }}-950/10 rounded-lg"></div>

    <div class="relative {{ $class }}">
        {{ $slot }}
    </div>
</div>
