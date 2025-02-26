@props([
    'title',
    'data'
])

<div class="bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6"
    x-data="{
        chart: null,
        init() {
            this.renderChart();
            Livewire.on('refreshChart', () => {
                if (this.chart) {
                    this.chart.destroy();
                }
                this.renderChart();
            });
        },
        renderChart() {
            const chartCtx = document.getElementById('{{ Str::slug($title) }}_chart').getContext('2d');
            this.chart = new Chart(chartCtx, {
                type: 'line',
                data: {
                    labels: @json(array_keys($data)),
                    datasets: [{
                        label: '{{ $title }}',
                        data: @json(array_values($data)),
                        borderColor: 'rgb(99, 102, 241)', // indigo-500
                        backgroundColor: 'rgba(99, 102, 241, 0.1)', // indigo-500 with opacity
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: 'rgb(100, 116, 139)' // slate-500
                            },
                            grid: {
                                color: 'rgba(100, 116, 139, 0.1)' // slate-500 with opacity
                            }
                        },
                        x: {
                            ticks: {
                                color: 'rgb(100, 116, 139)' // slate-500
                            },
                            grid: {
                                color: 'rgba(100, 116, 139, 0.1)' // slate-500 with opacity
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        crosshair: {
                            line: {
                                color: 'rgba(100, 116, 139, 0.3)',
                                width: 1
                            },
                            sync: {
                                enabled: true
                            },
                            zoom: {
                                enabled: true
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(30, 41, 59, 0.9)', // slate-800
                            titleColor: 'rgb(248, 250, 252)', // slate-50
                            bodyColor: 'rgb(248, 250, 252)', // slate-50
                            borderColor: 'rgb(100, 116, 139)', // slate-500
                            borderWidth: 1
                        }
                    }
                }
            });
        }
    }"
>
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 text-slate-400" />
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                {{ $title }}
            </h3>
        </div>
    </div>

    <div class="h-[300px]">
        <canvas id="{{ Str::slug($title) }}_chart"></canvas>
    </div>
</div>

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-crosshair"></script>
@endPushOnce
