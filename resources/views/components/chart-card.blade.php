@props([
    'title',
    'data'
])

<div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6" 
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
                        borderColor: 'rgb(129, 140, 248)', // indigo-400
                        backgroundColor: 'rgba(79, 70, 229, 0.1)', // indigo-600 with opacity
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
                                color: 'rgb(199, 210, 254)' // indigo-200
                            },
                            grid: {
                                color: 'rgba(199, 210, 254, 0.1)' // indigo-200 with opacity
                            }
                        },
                        x: {
                            ticks: {
                                color: 'rgb(199, 210, 254)' // indigo-200
                            },
                            grid: {
                                color: 'rgba(199, 210, 254, 0.1)' // indigo-200 with opacity
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        crosshair: {
                            line: {
                                color: 'rgb(199, 210, 254, 0.3)',
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
                            backgroundColor: 'rgba(79, 70, 229, 0.9)',
                            titleColor: 'rgb(224, 231, 255)',
                            bodyColor: 'rgb(224, 231, 255)',
                            borderColor: 'rgb(129, 140, 248)',
                            borderWidth: 1
                        }
                    }
                }
            });
        }
    }"
>
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="relative">
                    <div class="absolute inset-0 bg-indigo-800/20 blur-xl rounded-full"></div>
                    <div class="relative bg-gradient-to-br from-indigo-700 to-indigo-900 p-3 rounded-full">
                        <x-icon name="heroicon-o-chart-bar" class="w-6 h-6 text-indigo-100" />
                    </div>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-indigo-100">
                {{ $title }}
            </h3>
        </div>
    </div>

    <div style="height: 300px;">
        <canvas id="{{ Str::slug($title) }}_chart"></canvas>
    </div>
</div>

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-crosshair"></script>
@endPushOnce
