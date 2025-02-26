@props([
    'distribution' => null,
    'title' => 'Hourly Distribution',
    'description' => 'The hourly distribution shows the number of visits during each hour of the day.'
])

<div class="rounded-lg shadow-sm p-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100">{{ $title }}</h3>

        <div class="flex items-center gap-2">
            <span class="text-sm text-slate-500 dark:text-slate-400">
                <x-popover>
                    <x-slot:trigger>
                        <x-icon name="fas-info-circle" class="w-4 h-4 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-400 transition-colors" />
                    </x-slot:trigger>
                    <div class="text-sm text-slate-700 dark:text-slate-300">
                        {{ $description }}
                    </div>
                </x-popover>
        </div>
    </div>
    <div class="h-56">
        <canvas
            x-data="{
                chart: null,
                init() {
                    const hourlyData = {{ Js::from($distribution) }};
                    this.chart = new Chart(this.$el, {
                        type: 'bar',
                        data: {
                            labels: Array.from({length: 24}, (_, i) => `${String(i).padStart(2, '0')}:00`),
                            datasets: [{
                                data: hourlyData,
                                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                hoverBackgroundColor: 'rgba(16, 185, 129, 0.3)',
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    display: false
                                },
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: 'rgba(148, 163, 184, 0.1)'
                                    },
                                    ticks: {
                                        color: 'rgb(148, 163, 184)'
                                    }
                                }
                            }
                        }
                    });
                }
            }"
        ></canvas>
    </div>
    <div class="flex justify-between mt-1 text-xs text-slate-500">
        <span>{{ __('12:00 AM') }}</span>
        <span>{{ __('12:00 PM') }}</span>
        <span>{{ __('11:59 PM') }}</span>
    </div>
</div>
