<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <!-- Session Stats -->
    <div class="bg-white dark:bg-gray-600 p-6 rounded-xl shadow-lg border border-gray-50 dark:border-gray-500">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-50 mb-4 flex items-center gap-2">
            <x-icon name="heroicon-o-presentation-chart-line" class="w-5 h-5 text-gray-500 dark:text-gray-300" />
            {{ __('Session Overview') }}
        </h3>
        <div class="space-y-3">
            @foreach([
                'total_sessions' => [
                    'label' => 'Sessions',
                    'icon' => 'heroicon-o-users',
                    'format' => 'number',
                    'tooltip' => 'Total number of unique sessions during selected period'
                ],
                'total_pageviews' => [
                    'label' => 'Pageviews',
                    'icon' => 'heroicon-o-document-text',
                    'format' => 'number',
                    'tooltip' => 'Total number of pages viewed during selected period'
                ],
                'avg_session_duration' => [
                    'label' => 'Avg Session Duration',
                    'icon' => 'heroicon-o-clock',
                    'format' => 'string',
                    'tooltip' => 'Average time visitors spend on your site per session'
                ],
                'avg_pages_per_session' => [
                    'label' => 'Avg Pages/Session',
                    'icon' => 'heroicon-o-document-duplicate',
                    'format' => 'decimal',
                    'tooltip' => 'Average number of pages viewed per session'
                ],
                'bounce_rate' => [
                    'label' => 'Bounce Rate',
                    'icon' => 'heroicon-o-arrow-uturn-left',
                    'format' => 'percent',
                    'tooltip' => 'Percentage of visitors who leave after viewing only one page'
                ]
            ] as $key => $meta)
                <div class="flex gap-2 justify-between items-center">
                    <x-tooltip :text="__($meta['tooltip'])">
                        <span class="text-gray-500 dark:text-gray-300 flex items-center gap-2 text-sm">
                            <x-icon :name="$meta['icon']" class="w-5 h-5 text-gray-500 dark:text-gray-300" />
                            {{ __($meta['label']) }}
                        </span>
                    </x-tooltip>
                    <x-tooltip :text="__($meta['tooltip'])">
                        <span class="font-medium text-gray-700 dark:text-gray-50 text-sm">
                            @if($meta['format'] === 'string')
                                {{ $globalStats[$key] }}
                            @elseif($meta['format'] === 'decimal')
                                {{ number_format($globalStats[$key], 1) }}
                            @elseif($meta['format'] === 'percent')
                                {{ number_format($globalStats[$key], 1) }}%
                            @else
                                {{ number_format($globalStats[$key]) }}
                            @endif
                        </span>
                    </x-tooltip>
                </div>
            @endforeach
        </div>
    </div>

                <!-- Today's Stats -->
    <div class="bg-white dark:bg-gray-600 p-6 rounded-xl shadow-lg border border-gray-50 dark:border-gray-500">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-50 mb-4 flex items-center gap-2">
            <x-icon name="heroicon-o-calendar" class="w-5 h-5 text-gray-500 dark:text-gray-300" />
            {{ __('Today') }}
        </h3>
        <div class="space-y-3">
            @foreach([
                'sessions_today' => [
                    'label' => 'Sessions',
                    'icon' => 'heroicon-o-user-group',
                    'format' => 'number',
                    'tooltip' => 'Total number of unique sessions during selected period'
                ],
                'peak_hour_today' => [
                    'label' => 'Peak Hour',
                    'icon' => 'heroicon-o-clock',
                    'format' => 'hour',
                    'tooltip' => 'Hour with the highest number of sessions during selected period'
                ],
                'pageviews_today' => [
                    'label' => 'Pageviews',
                    'icon' => 'heroicon-o-document-text',
                    'format' => 'number',
                    'tooltip' => 'Total number of pages viewed during selected period'
                ],
                'avg_session_duration_today' => [
                    'label' => 'Avg Session Duration',
                    'icon' => 'heroicon-o-clock',
                    'format' => 'string',
                    'tooltip' => 'Average time visitors spend on your site per session'
                ],
                'bounce_rate_today' => [
                    'label' => 'Bounce Rate',
                    'icon' => 'heroicon-o-arrow-uturn-left',
                    'format' => 'percent',
                    'tooltip' => 'Percentage of visitors who leave after viewing only one page'
                ]
            ] as $key => $meta)
                <div class="flex gap-2 justify-between items-center">
                    <x-tooltip :text="__($meta['label'])">
                        <span class="text-gray-500 dark:text-gray-300 flex items-center gap-2 text-sm">
                            <x-icon :name="$meta['icon']" class="w-5 h-5" />
                            {{ __($meta['label']) }}
                        </span>
                    </x-tooltip>
                    <x-tooltip :text="__($meta['label'])">
                        <span class="font-medium text-gray-700 dark:text-gray-50 text-sm">
                            @if($meta['format'] === 'hour')
                                {{ $globalStats[$key] }}
                            @elseif($meta['format'] === 'string')
                                {{ $globalStats[$key] }}
                            @else
                                {{ number_format($globalStats[$key]) }}
                            @endif
                        </span>
                    </x-tooltip>
                </div>
            @endforeach
        </div>
    </div>

        <!-- This Week's Stats -->
        <div class="bg-white dark:bg-gray-600 p-6 rounded-xl shadow-lg border border-gray-50 dark:border-gray-500">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-50 mb-4 flex items-center gap-2">
            <x-icon name="heroicon-o-calendar" class="w-5 h-5 text-gray-500 dark:text-gray-300" />
            {{ __('This Week') }}
        </h3>
        <div class="space-y-3">
            @foreach([
                'sessions_yesterday' => [
                    'label' => 'Sessions',
                    'icon' => 'heroicon-o-user-group',
                    'format' => 'number',
                    'tooltip' => 'Total number of sessions yesterday'
                ],
                'sessions_this_week' => [
                    'label' => 'Sessions',
                    'icon' => 'heroicon-o-user-group',
                    'format' => 'number',
                    'tooltip' => 'Total number of sessions during selected period'
                ],
                'peak_hour_this_week' => [
                    'label' => 'Peak Hour',
                    'icon' => 'heroicon-o-clock',
                    'format' => 'hour',
                    'tooltip' => 'Hour with the highest number of sessions during selected period'
                ],
                'avg_time_per_visit_this_week' => [
                    'label' => 'Avg Time/Visit',
                    'icon' => 'heroicon-o-clock',
                    'format' => 'string',
                    'tooltip' => 'Average time visitors spend on your site per visit'
                ],
                'bounce_rate_this_week' => [
                    'label' => 'Bounce Rate',
                    'icon' => 'heroicon-o-arrow-uturn-left',
                    'format' => 'percent',
                    'tooltip' => 'Percentage of visitors who leave after viewing only one page'
                ]
            ] as $key => $meta)
                <div class="flex gap-2 justify-between items-center">
                    <x-tooltip :text="__($meta['label'])">
                        <span class="text-gray-500 dark:text-gray-300 flex items-center gap-2 text-sm">
                            <x-icon :name="$meta['icon']" class="w-5 h-5" />
                            {{ __($meta['label']) }}
                        </span>
                    </x-tooltip>
                    <x-tooltip :text="__($meta['label'])">
                        <span class="font-medium text-gray-700 dark:text-gray-50 text-sm">
                            @if($meta['format'] === 'hour')
                                {{ $globalStats[$key] }}:00
                            @else
                                {{ number_format($globalStats[$key]) }}
                            @endif
                        </span>
                    </x-tooltip>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Additional Metrics -->
    <div class="bg-white dark:bg-gray-600 p-6 rounded-xl shadow-lg border border-gray-50 dark:border-gray-500">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-50 mb-4 flex items-center gap-2">
            <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 text-gray-500 dark:text-gray-300" />
            {{ __('Engagement') }}
        </h3>
        <div class="space-y-3">
            @foreach([
                'avg_time_per_visit' => [
                    'label' => 'Avg Time/Visit',
                    'icon' => 'heroicon-o-clock',
                    'format' => 'string',
                    'tooltip' => 'Average time visitors spend on your site per visit'
                ],
                'returning_visitors' => [
                    'label' => 'Returning Visitors',
                    'icon' => 'heroicon-o-arrow-path',
                    'format' => 'number',
                    'tooltip' => 'Number of visitors who have returned to your site at least once'
                ],
                'engagement_rate' => [
                    'label' => 'Engagement Rate',
                    'icon' => 'heroicon-o-chart-bar',
                    'format' => 'percent',
                    'tooltip' => 'Percentage of visitors who have returned to your site at least once'
                ],
                'pages_per_session' => [
                    'label' => 'Pages/Session',
                    'icon' => 'heroicon-o-document-duplicate',
                    'format' => 'decimal',
                    'tooltip' => 'Average number of pages viewed per session'
                ],
                'avg_session_depth' => [
                    'label' => 'Avg Session Depth',
                    'icon' => 'heroicon-o-bars-3',
                    'format' => 'decimal',
                    'tooltip' => 'Average number of pages viewed per session'
                ],
            ] as $key => $meta)
                <div class="flex justify-between items-center">
                    <x-tooltip :text="__($meta['tooltip'])">
                        <span class="text-gray-500 dark:text-gray-300 flex items-center gap-2 text-sm">
                            <x-icon :name="$meta['icon']" class="w-5 h-5" />
                            {{ __($meta['label']) }}
                        </span>
                    </x-tooltip>
                    <x-tooltip :text="__($meta['tooltip'])">
                        <span class="font-medium text-gray-700 dark:text-gray-50 text-sm">
                            @if($meta['format'] == 'string')
                                {{ $globalStats[$key] }}
                            @elseif($meta['format'] === 'time')
                                {{ gmdate('H:i:s', $globalStats[$key]) }}
                            @elseif($meta['format'] === 'percent')
                                {{ number_format($globalStats[$key], 1) }}%
                            @elseif($meta['format'] === 'decimal')
                                {{ number_format($globalStats[$key], 1) }}
                            @else
                                {{ number_format($globalStats[$key]) }}
                            @endif
                        </span>
                    </x-tooltip>
                </div>
            @endforeach
        </div>
    </div>
</div>
