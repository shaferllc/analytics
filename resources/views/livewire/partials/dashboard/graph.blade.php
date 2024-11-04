
<div class="mb-10 mt-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                <x-icon name="heroicon-o-chart-bar" class="w-6 h-6 mr-2 fill-current" />
                {{ __('Trend Analysis') }}
            </h3>
            <div class="h-[500px]">
                <canvas id="trend-chart"></canvas>
            </div>
            <div class="flex flex-wrap justify-between items-center mt-4">
                <button id="resetZoomButton" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    {{ __('Reset Zoom') }}
                </button>
                <select id="chartTypeToggle" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded py-2 px-4 text-gray-700 dark:text-gray-200">
                    <option value="line">{{ __('Line') }}</option>
                    <option value="bar">{{ __('Bar') }}</option>
                    <option value="radar">{{ __('Radar') }}</option>
                    <option value="polarArea">{{ __('Polar Area') }}</option>
                    <option value="doughnut">{{ __('Doughnut') }}</option>
                </select>
                <button id="downloadButton" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    {{ __('Download Data') }}
                </button>
                <div class="flex items-center">
                    <span class="mr-2 text-gray-700 dark:text-gray-300">{{ __('Smoothing') }}:</span>
                    <input id="smoothingSlider" type="range" min="1" max="20" value="7" class="w-32">
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom"></script>
    
    <script>
        'use strict';

        window.addEventListener("DOMContentLoaded", function () {
            Chart.defaults.font.family = "Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji'";
            Chart.defaults.font.size = 12;

            const ctx = document.querySelector('#trend-chart').getContext('2d');
            
            const visitorsColor = window.getComputedStyle(document.getElementById('visitors-legend')).getPropertyValue('background-color');
            const pageViewsColor = window.getComputedStyle(document.getElementById('pageviews-legend')).getPropertyValue('background-color');

            let trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [
                        <?php foreach($pageviewsMap as $date => $value):
                            switch($range['unit']):
                                case 'hour':
                                    echo "'".\Carbon\Carbon::createFromFormat('H', $date)->format('H:i')."',";
                                    break;
                                case 'day':
                                    echo "'".__(':month :day', ['month' => mb_substr(__(\Carbon\Carbon::parse($date)->format('F')), 0, 3), 'day' => \Carbon\Carbon::parse($date)->format('j')])."',";
                                    break;
                                case 'month':
                                    echo "'".__(':year :month', ['year' => \Carbon\Carbon::parse($date)->format('Y'), 'month' => mb_substr(__(\Carbon\Carbon::parse($date)->format('F')), 0, 3)])."',";
                                    break;
                                default:
                                    echo "'".$date."',";
                            endswitch;
                        endforeach; ?>
                    ],
                    datasets: [{
                        label: '{{ __('Visitors') }}',
                        data: [
                            <?php foreach($visitorsMap as $date => $value):
                                echo $value.",";
                            endforeach; ?>
                        ],
                        borderColor: visitorsColor,
                        backgroundColor: visitorsColor,
                        fill: false,
                        tension: 0.1,
                        pointRadius: 0,
                        borderWidth: 2,
                        cubicInterpolationMode: 'monotone'
                    }, {
                        label: '{{ __('Pageviews') }}',
                        data: [
                            <?php foreach($pageviewsMap as $date => $value):
                                echo $value.",";
                            endforeach; ?>
                        ],
                        borderColor: pageViewsColor,
                        backgroundColor: pageViewsColor,
                        fill: false,
                        tension: 0.1,
                        pointRadius: 0,
                        borderWidth: 2,
                        cubicInterpolationMode: 'monotone'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString();
                                }
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
                        },
                        zoom: {
                            zoom: {
                                wheel: {
                                    enabled: true,
                                },
                                pinch: {
                                    enabled: true
                                },
                                mode: 'xy',
                            },
                            pan: {
                                enabled: true,
                                mode: 'xy',
                            }
                        },
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    }
                }
            });

            // Update chart colors when color scheme changes
            const observer = new MutationObserver(function() {
                const visitorsColor = window.getComputedStyle(document.getElementById('visitors-legend')).getPropertyValue('background-color');
                const pageViewsColor = window.getComputedStyle(document.getElementById('pageviews-legend')).getPropertyValue('background-color');

                trendChart.data.datasets[0].borderColor = visitorsColor;
                trendChart.data.datasets[0].backgroundColor = visitorsColor;
                trendChart.data.datasets[1].borderColor = pageViewsColor;
                trendChart.data.datasets[1].backgroundColor = pageViewsColor;

                trendChart.update();
            });

            observer.observe(document.querySelector('html'), { attributes: true });

            // Reset zoom button
            document.getElementById('resetZoomButton').addEventListener('click', () => {
                trendChart.resetZoom();
            });

            // Chart type toggle
            document.getElementById('chartTypeToggle').addEventListener('change', (e) => {
                trendChart.config.type = e.target.value;
                trendChart.update();
            });

            // Data point highlighting
            ctx.canvas.addEventListener('click', (evt) => {
                const points = trendChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
                if (points.length) {
                    const firstPoint = points[0];
                    const label = trendChart.data.labels[firstPoint.index];
                    const value = trendChart.data.datasets[firstPoint.datasetIndex].data[firstPoint.index];
                    alert(`${label}: ${value}`);
                }
            });

            // Moving average line
            function calculateMovingAverage(data, windowSize) {
                const result = [];
                for (let i = 0; i < data.length; i++) {
                    const window = data.slice(Math.max(0, i - windowSize + 1), i + 1);
                    const average = window.reduce((sum, num) => sum + num, 0) / window.length;
                    result.push(average);
                }
                return result;
            }

            const movingAverageDataset = {
                label: 'Moving Average',
                data: calculateMovingAverage(trendChart.data.datasets[0].data, 7),
                borderColor: 'rgba(255, 206, 86, 1)',
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderWidth: 2,
                fill: false,
            };
            trendChart.data.datasets.push(movingAverageDataset);
            trendChart.update();

            // Download data button
            document.getElementById('downloadButton').addEventListener('click', () => {
                const csvContent = "data:text/csv;charset=utf-8," 
                    + trendChart.data.labels.join(",") + "\n"
                    + trendChart.data.datasets.map(dataset => dataset.data.join(",")).join("\n");
                const encodedUri = encodeURI(csvContent);
                const link = document.createElement("a");
                link.setAttribute("href", encodedUri);
                link.setAttribute("download", "chart_data.csv");
                document.body.appendChild(link);
                link.click();
            });

            // Data smoothing slider
            document.getElementById('smoothingSlider').addEventListener('input', (e) => {
                const windowSize = parseInt(e.target.value);
                trendChart.data.datasets[2].data = calculateMovingAverage(trendChart.data.datasets[0].data, windowSize);
                trendChart.update();
            });
        });
    </script>
