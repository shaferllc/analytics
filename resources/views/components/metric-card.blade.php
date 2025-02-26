<div class="bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6 hover:shadow-xl transition-shadow duration-200">
    <div class="flex items-start justify-between">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                @isset($icon)
                    <x-dynamic-component
                        :component="$icon"
                        class="w-5 h-5 text-slate-400 dark:text-slate-500"
                    />
                @endisset
                <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $title }}</h3>
            </div>
            <p class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                @if(is_numeric($value))
                    {{ number_format($value, $precision ?? 0) }}
                @else
                    {{ $value }}
                @endif
            </p>
        </div>

        @isset($badge)
            <span @class([
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-800/30 dark:text-emerald-200' => $badge['type'] === 'success',
                'bg-rose-100 text-rose-800 dark:bg-rose-800/30 dark:text-rose-200' => $badge['type'] === 'error',
                'bg-amber-100 text-amber-800 dark:bg-amber-800/30 dark:text-amber-200' => $badge['type'] === 'warning',
                'bg-indigo-100 text-indigo-800 dark:bg-indigo-800/30 dark:text-indigo-200' => $badge['type'] === 'info'
            ])>
                {{ $badge['label'] }}
            </span>
        @endisset
    </div>

    @isset($change)
        <div class="mt-4 flex items-center space-x-2">
            <span @class([
                'inline-flex items-center text-sm font-semibold',
                'text-emerald-500 dark:text-emerald-400' => $change >= 0,
                'text-rose-500 dark:text-rose-400' => $change < 0
            ])>
                <x-dynamic-component
                    :component="$change >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down'"
                    class="w-4 h-4 mr-1"
                />
                {{ number_format(abs($change), 1) }}%
            </span>
            <span class="text-sm text-slate-500 dark:text-slate-400">vs previous</span>
        </div>
    @endisset

    @isset($chart)
        <div class="mt-4 relative">
            <div class="absolute inset-0 bg-gradient-to-t from-white/90 dark:from-slate-800/90 via-white/50 dark:via-slate-800/50 to-transparent pointer-events-none"></div>
            <canvas
                x-data="{
                    chart: null,
                    init() {
                        const chartData = {{ json_encode($chart) }};
                        this.chart = new Chart(this.$refs.canvas, {
                            type: 'line',
                            data: {
                                labels: Object.keys(chartData),
                                datasets: [{
                                    label: '{{ $title }}',
                                    data: Object.values(chartData),
                                    borderColor: '#6366f1',
                                    borderWidth: 2,
                                    fill: false,
                                    tension: 0.4,
                                    pointRadius: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        enabled: false
                                    }
                                },
                                scales: {
                                    y: {
                                        display: false,
                                        beginAtZero: true
                                    },
                                    x: {
                                        display: false
                                    }
                                },
                                interaction: {
                                    intersect: false,
                                    mode: 'index'
                                }
                            }
                        });
                    }
                }"
                x-ref="canvas"
                class="h-16"
            ></canvas>
        </div>
    @endisset

    @isset($footer)
        <div class="mt-4 pt-4 border-t border-slate-200/60 dark:border-slate-700/60 text-sm text-slate-500 dark:text-slate-400">
            {{ $footer }}
        </div>
    @endisset
</div>
