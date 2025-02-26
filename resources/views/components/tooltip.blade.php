@props([
    'text' => '',
    'position' => 'bottom',
    'icon' => null,
    'darkMode' => true
])

<div x-data="{ tooltip: false }"
     @mouseenter="tooltip = true"
     @mouseleave="tooltip = false"
     {{ $attributes->merge(['class' => 'relative']) }}>
    {{ $slot }}

    <div x-show="tooltip"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 px-3 py-2 text-sm rounded-lg shadow-lg whitespace-nowrap"
         :class="{
            'bottom-full left-1/2 -translate-x-1/2 mb-2': '{{ $position }}' === 'top',
            'top-full left-1/2 -translate-x-1/2 mt-2': '{{ $position }}' === 'bottom',
            'right-full top-1/2 -translate-y-1/2 mr-2': '{{ $position }}' === 'left',
            'left-full top-1/2 -translate-y-1/2 ml-2': '{{ $position }}' === 'right',
            'bg-white border border-slate-200 text-slate-700': !$darkMode,
            'bg-slate-800/90 border border-slate-700/60 text-slate-100': $darkMode
         }">
         @if($icon)
            <div class="flex items-center gap-2">
                <x-icon :name="$icon" class="w-4 h-4" />
                <span>{{ $text }}</span>
            </div>
         @else
            {{ $text }}
         @endif
    </div>
</div>
