<div class="bg-white/80 dark:bg-gray-800/90 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-gray-200/50 dark:border-gray-700/50">
    <div class="flex items-center gap-4 mb-4">
        <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg shadow-indigo-500/20">
            <x-icon name="heroicon-o-chart-pie" class="w-6 h-6 text-white" />
        </div>
        <h3 class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400">{{ __('Distribution') }}</h3>
    </div>
    <div class="h-48">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <canvas x-data="{
            init() {
                new Chart(this.$el, {
                    type: 'doughnut',
                    data: {
                        labels: ['Valid', 'Expiring Soon', 'Expired', 'No Certificate'],
                        datasets: [{
                            data: [
                                {{ $website->certificates->where('was_valid', true)->where('valid_to', '>', now()->addDays(30))->count() }},
                                {{ $website->certificates->where('valid_to', '>', now())->where('valid_to', '<=', now()->addDays(30))->count() }},
                                {{ $website->certificates->where('did_expire', true)->count() }},
                                {{ $website->certificates->filter(fn($cert) => !$cert->was_valid)->count() }}
                            ],
                            backgroundColor: ['#10B981', '#8B5CF6', '#EF4444', '#6B7280'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { padding: 15 }
                            }
                        }
                    }
                })
            }
        }"></canvas>
    </div>
</div>