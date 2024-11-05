<div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-blue-800 text-white">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="p-4" wire:ignore>
            <div class="h-[230px]"> 
                <canvas id="realtime-chart" x-ref="realtimeChart"></canvas>
            </div>
        </div>

        @pushOnce('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        @endpushOnce

        @push('scripts')
            <script>
                'use strict';

                // Initialize chart outside to make it accessible
                let realtimeChart;

                document.addEventListener("DOMContentLoaded", function () {
                    const canvas = document.querySelector('#realtime-chart');
                    const ctx = canvas.getContext('2d');

                    // Set default chart font
                    Chart.defaults.font = {
                        family: "Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'",
                        size: 12
                    };

                    // Create the chart
                    realtimeChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: Object.keys(@json($this->visitorCounts)).map(date => {
                                return new Date(date).toLocaleTimeString();
                            }),
                            datasets: [{
                                label: '{{ __('Visitors') }}',
                                data: Object.values(@json($this->visitorCounts)),
                                fill: false,
                                tension: 0.1,
                                pointRadius: 2,
                                borderWidth: 2,
                                cubicInterpolationMode: 'monotone'
                            },
                            {
                                label: '{{ __('Pageviews') }}',
                                data: Object.values(@json($this->pageviewCounts)),
                                fill: false,
                                tension: 0.1,
                                pointRadius: 2,
                                borderWidth: 2,
                                cubicInterpolationMode: 'monotone'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 0
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        precision: 0
                                    }
                                },
                                x: {
                                    ticks: {
                                        maxRotation: 45,
                                        minRotation: 45
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                });

                // Listen for Livewire updates
                Livewire.on('visitorCountsUpdated', (data) => {
                    if (realtimeChart) {
                        realtimeChart.data.labels = Object.keys(data).map(date => {
                            return new Date(date).toLocaleTimeString();
                        });
                        realtimeChart.data.datasets[0].data = Object.values(data);
                        realtimeChart.update('none'); // Update without animation
                    }
                });

                Livewire.on('pageviewCountsUpdated', (data) => {
                    if (realtimeChart) {
                        realtimeChart.data.datasets[1].data = Object.values(data);
                        realtimeChart.update('none'); // Update without animation
                    }
                });
            </script>
        @endpush
    </div>
</div>
