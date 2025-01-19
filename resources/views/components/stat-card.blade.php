@props([
    'title',
    'value',
    'oldValue' => null,
    'icon',
    'color' => 'indigo'
])

<div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="relative">
                    <div class="absolute inset-0 bg-indigo-800/20 blur-xl rounded-full"></div>
                    <div class="relative bg-gradient-to-br from-indigo-700 to-indigo-900 p-3 rounded-full">
                        <x-dynamic-component 
                            :component="$icon"
                            class="h-6 w-6 text-indigo-100"
                        />
                    </div>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-indigo-100">
                    {{ $title }}
                </h3>
                <div class="flex items-baseline">
                    <div class="text-2xl font-bold text-indigo-100">
                        {{ $value }}
                    </div>

                    @if($oldValue !== null)
                        @php
                            $percentChange = $oldValue != 0 ? (($value - $oldValue) / $oldValue) * 100 : 0;
                            $isIncrease = $percentChange > 0;
                        @endphp
                        <div class="ml-2 flex items-baseline text-sm font-semibold {{ $isIncrease ? 'text-green-400' : 'text-red-400' }}">
                            <span>{{ number_format(abs($percentChange), 1) }}%</span>
                            @if($isIncrease)
                                <x-heroicon-s-arrow-trending-up class="h-4 w-4 ml-1" />
                            @else
                                <x-heroicon-s-arrow-trending-down class="h-4 w-4 ml-1" />
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
