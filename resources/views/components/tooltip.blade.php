@props([
    'text' => '',
    'position' => 'bottom'
])

<div x-data="{ tooltip: false }" 
     @mouseenter="tooltip = true" 
     @mouseleave="tooltip = false"
     {{ $attributes->merge(['class' => 'relative]) }}>
    {{ $slot }}
    <div x-show="tooltip"
         x-transition:enter="transition ease-out duration-100" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 px-2 py-1 text-sm text-white bg-black rounded whitespace-nowrap"
         :class="{
            'bottom-full left-1/2 -translate-x-1/2 mb-1': '{{ $position }}' === 'top',
            'top-full left-1/2 -translate-x-1/2 mt-1': '{{ $position }}' === 'bottom',
            'right-full top-1/2 -translate-y-1/2 mr-1': '{{ $position }}' === 'left',
            'left-full top-1/2 -translate-y-1/2 ml-1': '{{ $position }}' === 'right'
         }">
        {{ $text }}
    </div>
</div>
