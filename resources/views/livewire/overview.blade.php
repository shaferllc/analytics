<div>
    <div>
        <div class="flex items-center">    
            <x-icon name="heroicon-o-presentation-chart-line" class="fill-current w-6 h-6 mr-2" />
            <h2 class="text-xl font-semibold text-gray-800">{{ __('Overview') }}</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 shadow-md">
                <div class="flex items-center gap-2">
                    <div class="block align-items-center justify-content-center bg-primary rounded w-4 h-4 flex-shrink-0" id="visitors-legend"></div>
                    <h3 class="text-sm font-bold text-green-800">{{ __('Visitors') }}</h3>
                </div>

                <div class="text-2xl font-bold text-green-900">{{ number_format($totalVisitors, 0, __('.'), __(',')) }}</div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        @include('analytics::livewire.partials.growth', ['growthCurrent' => $totalVisitors, 'growthPrevious' => $totalVisitorsOld, 'type' => 'visitors'])
                    </div>
                </div>
            </div>

            <!-- Pageviews -->
            <div class="bg-blue-50 rounded-lg p-4 shadow-md">
                <div class="flex items-center gap-2">
                    <div class="block align-items-center justify-content-center bg-secondary rounded w-4 h-4 flex-shrink-0" id="pageviews-legend"></div>
                    <h3 class="text-sm font-bold text-red-800">{{ __('Pageviews') }}</h3>
                </div>

                <div class="text-2xl font-bold text-red-900">{{ number_format($totalPageviews, 0, __('.'), __(',')) }}</div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        @include('analytics::livewire.partials.growth', ['growthCurrent' => $totalPageviews, 'growthPrevious' => $totalPageviewsOld, 'type' => 'pageviews'])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Graph -->
    @include('analytics::livewire.partials.dashboard.graph')


    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Pages -->
        @include('analytics::livewire.partials.dashboard.pages')

        <!-- Referrers -->
        @include('analytics::livewire.partials.dashboard.referrers')

        <!-- Countries -->
        @include('analytics::livewire.partials.dashboard.countries')

        <!-- Browsers -->
        @include('analytics::livewire.partials.dashboard.browsers')

        <!-- Operating systems -->
        @include('analytics::livewire.partials.dashboard.operating-systems')

        <!-- Events -->
        @include('analytics::livewire.partials.dashboard.events')

    </div>
</div>
