<div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-blue-800 text-white">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @include('livewire.partials.tabs')
        @include('livewire.websites.header')
        <div class="flex-1 bg-gray-700 rounded-2xl shadow-lg border border-gray-600 hover:border-blue-500 transform hover:-translate-y-1 transition-all duration-300 p-4">
            <div class="flex items-center justify-between gap-4 mb-4">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <x-icon name="heroicon-o-presentation-chart-line" class="w-6 h-6 mr-2" />
                    {{ __('Overview') }}
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
                <div class="bg-gray-800 rounded-lg p-4 shadow-md">
                    <!-- <div class="flex items-center gap-2">
                        <div class="block align-items-center justify-content-center bg-primary rounded w-4 h-4 flex-shrink-0" id="visitors-legend"></div>
                        <h3 class="text-sm font-bold text-blue-400">{{ __('Visitors') }}</h3>
                    </div> -->

                    <!-- <div class="text-2xl font-bold text-white">{{ number_format($totalVisitors, 0, __('.'), __(',')) }}</div> -->
                    @include('analytics::livewire.partials.growth', ['growthCurrent' => $totalVisitors, 'growthPrevious' => $totalVisitorsOld, 'type' => 'visitors'])
                </div>

                <!-- Pageviews -->
                <div class="bg-gray-800 rounded-lg p-4 shadow-md">
                    <!-- <div class="flex items-center gap-2">
                        <div class="block align-items-center justify-content-center bg-secondary rounded w-4 h-4 flex-shrink-0" id="pageviews-legend"></div>
                        <h3 class="text-sm font-bold text-blue-400">{{ __('Pageviews') }}</h3>
                    </div> -->

                    <!-- <div class="text-2xl font-bold text-white">{{ number_format($totalPageviews, 0, __('.'), __(',')) }}</div> -->
                    @include('analytics::livewire.partials.growth', ['growthCurrent' => $totalPageviews, 'growthPrevious' => $totalPageviewsOld, 'type' => 'pageviews'])
                </div>
            </div>
        </div>

        <!-- Graph -->
        @include('analytics::livewire.partials.dashboard.graph')


        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Pages -->
            include('analytics::livewire.partials.dashboard.pages')

            <!-- Referrers -->
            include('analytics::livewire.partials.dashboard.referrers')

            <!-- Countries -->
            include('analytics::livewire.partials.dashboard.countries')

            <!-- Browsers -->
            include('analytics::livewire.partials.dashboard.browsers')

            <!-- Operating systems -->
            include('analytics::livewire.partials.dashboard.operating-systems')

            <!-- Events -->
            include('analytics::livewire.partials.dashboard.events')

        </div>
    </div>
</div>