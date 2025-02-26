<x-site :site="$site">
    <div class="space-y-8">
        <x-breadcrumbs :breadcrumbs="[
            ['url' => route('sites.analytics.overview', ['site' => $site->id]), 'label' => __('Analytics Dashboard')]
        ]" />

        <!-- Primary Metrics Section -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <x-heroicon-o-chart-bar class="w-6 h-6" />
                {{ __('Key Metrics') }}
                <x-badge color="blue" class="text-sm">{{ __('Last 30 Days') }}</x-badge>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                   //[
                   //    'title' => __('Total Visitors'),
                   //    'value' => number_format($this->totalVisitors['current']),
                   //    'change' => $this->totalVisitors['old'] ? (($this->totalVisitors['current'] - $this->totalVisitors['old']) / $this->totalVisitors['old']) * 100 : 0,
                   //    'icon' => 'heroicon-o-users',
                   //    'chart' => $this->totalVisitors,
                   //    'tooltip' => __('Unique visitors to your site'),
                   //    'footer' => __('Total visitors for the selected time range'),
                   //    'badge' => [
                   //        'label' => __('New'),
                   //        'type' => 'success'
                   //    ]
                   //],
                   //[
                   //    'title' => __('Total Pageviews'),
                   //    'value' => number_format($this->totalPageviews['current']),
                   //    'change' => $this->totalPageviews['old'] ? (($this->totalPageviews['current'] - $this->totalPageviews['old']) / $this->totalPageviews['old']) * 100 : 0,
                   //    'icon' => 'heroicon-o-document-text',
                   //    'chart' => $this->totalPageviews,
                   //    'tooltip' => __('Total pages viewed by all visitors'),
                   //    'footer' => __('Total pageviews for the selected time range'),
                   //    'badge' => [
                   //        'label' => __('New'),
                   //        'type' => 'success'
                   //    ]
                   //],
                   //[
                   //    'title' => __('Avg Session Duration'),
                   //    'value' => gmdate('H:i:s', $this->avgSessionDuration),
                   //    'icon' => 'heroicon-o-clock',
                   //    'chart' => $this->avgSessionDuration,
                   //    'tooltip' => __('Average time spent per session'),
                   //    'footer' => __('Average time spent per session for the selected time range'),
                   //    'badge' => [
                   //        'label' => __('New'),
                   //        'type' => 'success'
                   //    ]
                   //],
                   //[
                   //    'title' => __('Pages/Session'),
                   //    'value' => number_format($this->pagesPerSession, 1),
                   //    'icon' => 'heroicon-o-document-duplicate',
                   //    'chart' => $this->pagesPerSession,
                   //    'tooltip' => __('Average pages viewed per session'),
                   //    'footer' => __('Average pages viewed per session for the selected time range'),
                   //    'badge' => [
                   //        'label' => __('New'),
                   //        'type' => 'success'
                   //    ]
                   //]
                ] as $metric)
                    <x-analytics::metric-card
                        :title="$metric['title']"
                        :value="$metric['value']"
                        :change="$metric['change'] ?? null"
                        :icon="$metric['icon']"
                        :chart="$metric['chart']"
                        :tooltip="$metric['tooltip'] ?? null"
                    />
                @endforeach
            </div>
        </section>

        <!-- Engagement Section -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <x-heroicon-o-hand-thumb-up class="w-6 h-6" />
                {{ __('Engagement') }}
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                   //[
                   //    'title' => __('Bounce Rate'),
                   //    'value' => number_format($this->bounceRate, 1) . '%',
                   //    'icon' => 'heroicon-o-arrow-uturn-left',
                   //    'chart' => $this->bounceRate,
                   //    'tooltip' => __('Percentage of visitors who leave after viewing only one page'),
                   //    'badge' => [
                   //        'label' => __('New'),
                   //        'type' => 'success'
                   //    ]
                   //],
                   //[
                   //    'title' => __('New Visitors'),
                   //    'value' => number_format($this->newVsReturningVisitors['new']),
                   //    'icon' => 'heroicon-o-sparkles',
                   //    'chart' => $this->newVsReturningVisitors,
                   //    'tooltip' => __('New visitors to your site'),
                   //    'badge' => [
                   //        'label' => __('New'),
                   //        'type' => 'success'
                   //    ]
                   //],
                   //[
                   //    'title' => __('Returning Visitors'),
                   //    'value' => number_format($this->newVsReturningVisitors['returning']),
                   //    'icon' => 'heroicon-o-arrow-path',
                   //    'chart' => $this->newVsReturningVisitors,
                   //    'tooltip' => __('Returning visitors to your site'),
                   //    'badge' => [
                   //        'label' => __('New'),
                   //        'type' => 'success'
                   //    ]
                   //]
                ] as $metric)
                    <x-analytics::metric-card
                        :title="$metric['title']"
                        :value="$metric['value']"
                        :change="$metric['change'] ?? null"
                        :icon="$metric['icon']"
                        :chart="$metric['chart']"
                        :tooltip="$metric['tooltip'] ?? null"
                        :badge="$metric['badge'] ?? null"
                    />
                @endforeach
            </div>
        </section>

        <!-- Data Visualization Section -->
        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                    <x-heroicon-o-presentation-chart-line class="w-6 h-6" />
                    {{ __('Trends') }}
                </h2>
                <div class="flex items-center gap-2">
                    <x-ts-button wire:click="setTimeRange('7d')" :active="$daterange === '7d'">7 Days</x-ts-button>
                    <x-ts-button wire:click="setTimeRange('30d')" :active="$daterange === '30d'">30 Days</x-ts-button>
                    <x-forms.button wire:click="setTimeRange('90d')" :active="$daterange === '90d'">90 Days</x-forms.button>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                x-analytics::chart-card
                    title="Visitors by Country"
                    type="bar"
                    :labels="array_keys($this->visitorsMap)"
                    :data="array_values($this->visitorsMap)"
                    :colors="$this->chartColors"
                />

                x-analytics::chart-card
                    title="Pageviews Over Time"
                    type="line"
                    :labels="array_keys($this->pageviewsMap)"
                    :data="array_values($this->pageviewsMap)"
                    :colors="$this->chartColors"
                />


                x-analytics::chart-card
                    title="Traffic Sources"
                    type="doughnut"
                    :labels="['Direct', 'Referral', 'Social', 'Search']"
                    :data="[$this->trafficSources['direct'], $this->trafficSources['referral'], $this->trafficSources['social'], $this->trafficSources['search']]"
                    :colors="$this->chartColors"
                />

                x-analytics::chart-card
                    title="Device Distribution"
                    type="pie"
                    :labels="['Desktop', 'Mobile', 'Tablet']"
                    :data="[$this->deviceDistribution['desktop'], $this->deviceDistribution['mobile'], $this->deviceDistribution['tablet']]"
                    :colors="$this->chartColors"
                />
            </div>
        </section>

        <!-- Insights Section -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <x-heroicon-o-light-bulb class="w-6 h-6" />
                {{ __('Insights') }}
            </h2>
            @ds($this->browsers)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach([
                   [
                        'title' => __('Top Pages'),
                        'items' => $this->pageViews,
                        'total' => __('Total Pageviews: :total', ['total' => $this->pageViews->total_page_views_count]),
                        'type' => 'page',
                        'translate' => false
                    ],
                   [
                        'title' => __('Top Countries'),
                        'items' => $this->countries,
                        'total' => $this->countries->sum('count'),
                        'type' => 'country',
                        'translate' => false
                   ],
                   [
                        'title' => __('Top Browsers'),
                        'items' => $this->browsers,
                        'total' => $this->browsers->sum('count'),
                        'type' => 'browser',
                   ],
                   //[
                   //     'title' => __('Top Devices'),
                   //     'items' => collect($this->devicesMap)->sortDesc()->take(10),
                   //     'total' => collect($this->devicesMap)->values()->sum(),
                   //     'type' => 'device',
                   //],
                   //[
                   //     'title' => __('Top Operating Systems'),
                   //     'items' => collect($this->operatingSystemsMap)->sortDesc()->take(10),
                   //     'total' => collect($this->operatingSystemsMap)->values()->sum(),
                   //     'type' => 'os',
                   //     'translate' => true
                   //],
                   //[
                   //     'title' => __('Top Referrers'),
                   //     'items' => collect($this->referrersMap)->sortDesc()->take(10),
                   //     'total' => collect($this->referrersMap)->values()->sum(),
                   //     'type' => 'referrer',
                   //     'translate' => false
                   // ]
                ] as $list)
                    <x-analytics::list-card
                        :title="Arr::get($list, 'title')"
                        :items="Arr::get($list, 'items')"
                        :total="Arr::get($list, 'total')"
                        :type="Arr::get($list, 'type')"
                        :translate="Arr::get($list, 'translate')"
                    />
                @endforeach
            </div>
        </section>
    </div>
</x-site>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('chartUpdate', (chartId, data) => {
                const chart = Chart.getChart(chartId);
                if (chart) {
                    chart.data.labels = data.labels;
                    chart.data.datasets[0].data = data.data;
                    chart.update();
                }
            });
        });
    </script>
@endpush
