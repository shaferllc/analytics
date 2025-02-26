@props([
    'title',
    'value',
    'oldValue' => null,
    'icon',
    'color' => 'slate'
])

<div class="bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6 hover:bg-white/50 dark:hover:bg-slate-700/30 transition-colors">
    <div class="flex items-center gap-4">
        <div class="p-3 rounded-lg bg-slate-100/50 dark:bg-slate-700/20">
            <x-dynamic-component
                :component="$icon"
                class="w-6 h-6 text-slate-500 dark:text-slate-400"
            />
        </div>

        <div class="space-y-1">
            <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">
                {{ $title }}
            </h3>
            <div class="flex items-baseline gap-2">
                <p class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    {{ $value }}
                </p>

                @if($oldValue !== null)
                    @php
                        $percentChange = $oldValue != 0 ? (($value - $oldValue) / $oldValue) * 100 : 0;
                        $isIncrease = $percentChange > 0;
                        $changeColor = $isIncrease ? 'text-emerald-500' : 'text-rose-500';
                    @endphp
                    <div class="flex items-center text-sm font-medium {{ $changeColor }}">
                        <span>{{ number_format(abs($percentChange), 1) }}%</span>
                        <x-dynamic-component
                            :component="$isIncrease ? 'heroicon-s-arrow-trending-up' : 'heroicon-s-arrow-trending-down'"
                            class="w-4 h-4 ml-1"
                        />
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
