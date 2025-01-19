<x-site :site="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('sites.analytics.overview', ['site' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('sites.analytics.acquisitions', ['site' => $site->id]),
                'label' => __('Acquisitions'),
                'icon' => 'heroicon-o-cursor-arrow-rays',
            ],
            [
                'url' => route('sites.analytics.sessions', ['site' => $site->id]),
                'label' => __('Sessions'),
                'icon' => 'heroicon-o-user-group',
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Sessions')"
            :description="__('Track visitor sessions on your website.')"
            :icon="'heroicon-o-user-group'"
            :totalText="__('Total Sessions')"
            :paginator="$visitors"
            :total="$visitors->total()"
            :data="$visitors"
            :totalPageviews="$globalStats['total_pageviews']"
            :site="$site"
            :daterange="$daterange"
            :perPage="$perPage"
            :sortBy="$sortBy"
            :sort="$sort"
            :from="$from"
            :sortWords="['count' => __('Sessions'), 'value' => __('Session ID')]"
            :to="$to"
            :search="$search"
        />

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
                            'label' => 'Total Sessions',
                            'icon' => 'heroicon-o-users',
                            'format' => 'number',
                            'tooltip' => 'Total number of unique sessions during selected period'
                        ],
                        'total_pageviews' => [
                            'label' => 'Total Pageviews',
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
                                <span class="font-medium text-gray-700 dark:text-gray-50">
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
                            'format' => 'number'
                        ],
                        'peak_hour_today' => [
                            'label' => 'Peak Hour',
                            'icon' => 'heroicon-o-clock',
                            'format' => 'hour'
                        ],

                    ] as $key => $meta)
                        <div class="flex gap-2 justify-between items-center">
                            <x-tooltip :text="__($meta['label'])">
                                <span class="text-gray-500 dark:text-gray-300 flex items-center gap-2">
                                    <x-icon :name="$meta['icon']" class="w-5 h-5" />
                                    {{ __($meta['label']) }}
                                </span>
                            </x-tooltip>
                            <x-tooltip :text="__($meta['label'])">
                                <span class="font-medium text-gray-700 dark:text-gray-50">
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

            <!-- This Week's Stats -->
            <div class="bg-white dark:bg-gray-600 p-6 rounded-xl shadow-lg border border-gray-50 dark:border-gray-500">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-50 mb-4 flex items-center gap-2">
                    <x-icon name="heroicon-o-calendar" class="w-5 h-5 text-gray-500 dark:text-gray-300" />
                    {{ __('This Week') }}
                </h3>
                <div class="space-y-3">
                    @foreach([
                        'sessions_yesterday' => [
                            'label' => 'Yesterday',
                            'icon' => 'heroicon-o-clock',
                            'format' => 'number',
                            'tooltip' => 'Total number of sessions yesterday'
                        ],
                        'sessions_this_week' => [
                            'label' => 'Sessions',
                            'icon' => 'heroicon-o-user-group',
                            'format' => 'number',
                            'tooltip' => 'Total number of sessions during selected period'
                        ],
                        'sessions_last_week' => [
                            'label' => 'Last Week',
                            'icon' => 'heroicon-o-clock',
                            'format' => 'number',
                            'tooltip' => 'Total number of sessions last week'
                        ],

                        'peak_hour_this_week' => [
                            'label' => 'Peak Hour',
                            'icon' => 'heroicon-o-clock',
                            'format' => 'hour',
                            'tooltip' => 'Hour with the highest number of sessions during selected period'
                        ]
                    ] as $key => $meta)
                        <div class="flex gap-2 justify-between items-center">
                            <x-tooltip :text="__($meta['label'])">
                                <span class="text-gray-500 dark:text-gray-300 flex items-center gap-2">
                                    <x-icon :name="$meta['icon']" class="w-5 h-5" />
                                    {{ __($meta['label']) }}
                                </span>
                            </x-tooltip>
                            <x-tooltip :text="__($meta['label'])">
                                <span class="font-medium text-gray-700 dark:text-gray-50">
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
                            'format' => 'time'
                        ],
                        'returning_visitors' => [
                            'label' => 'Returning Visitors',
                            'icon' => 'heroicon-o-arrow-path',
                            'format' => 'number'
                        ],
                        'engagement_rate' => [
                            'label' => 'Engagement Rate',
                            'icon' => 'heroicon-o-chart-bar',
                            'format' => 'percent'
                        ]
                    ] as $key => $meta)
                        <div class="flex justify-between items-center">
                            <
                            <span class="text-gray-500 dark:text-gray-300 flex items-center gap-2">
                                <x-icon :name="$meta['icon']" class="w-5 h-5" />
                                {{ __($meta['label']) }}
                            </span>
                            <span class="font-medium text-gray-700 dark:text-gray-50">
                                @if($meta['format'] === 'time')
                                    {{ gmdate('H:i:s', $globalStats[$key]) }}
                                @elseif($meta['format'] === 'percent')
                                    {{ number_format($globalStats[$key], 1) }}%
                                @else
                                    {{ number_format($globalStats[$key]) }}
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div>
            @if($visitors->isEmpty())
                <x-analytics::no-results />
            @else
                <div x-data="{ view: 'list' }" class="space-y-4">
                    <x-analytics::view-switcher :paginator="$visitors" />

                    <x-analytics::view view="list" class="bg-white dark:bg-gray-600 rounded-xl shadow-lg border border-gray-50 dark:border-gray-500 p-6 space-y-6">
                        @foreach($visitors as $visitor)
                            <div x-data="{ open: false }" class="flex flex-col space-y-6 border-b border-gray-50 dark:border-gray-500 pb-6 last:border-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-blue-50 dark:bg-gray-500 p-3 rounded-full">
                                            <x-icon name="heroicon-o-user-group" class="w-5 h-5 text-blue-400 dark:text-blue-200" />
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-50 max-w-full flex items-center space-x-3">
                                                <span>{{ $visitor['session_id'] }}</span>
                                                <span class="text-sm text-gray-400 dark:text-gray-100">#{{ $visitor['id'] }}</span>
                                            </h3>
                                            <div class="mt-1 flex flex-wrap items-center gap-2 text-gray-500 dark:text-gray-100 text-sm">
                                                @foreach($visitor['city'] as $index => $city)
                                                    @if($city && isset($visitor['country'][$index]) && isset($visitor['continent'][$index]))
                                                        <span class="bg-blue-50 dark:bg-gray-500 px-2 py-1 rounded">
                                                            {{ $city }}, {{ $visitor['country'][$index] }}, {{ $visitor['continent'][$index] }}
                                                        </span>
                                                    @endif
                                                @endforeach

                                                @if($visitor['timezone'])
                                                    <span class="bg-blue-50 dark:bg-gray-500 px-2 py-1 rounded">
                                                        <x-icon name="heroicon-o-clock" class="w-4 h-4 inline mr-1" />
                                                        {{ $visitor['timezone'] }}
                                                    </span>
                                                @endif

                                                @if($visitor['language'])
                                                    <span class="bg-blue-50 dark:bg-gray-500 px-2 py-1 rounded">
                                                        <x-icon name="heroicon-o-language" class="w-4 h-4 inline mr-1" />
                                                        {{ $visitor['language'] }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="flex flex-col items-end space-y-2">
                                            @foreach([
                                                [
                                                    'icon' => 'clock',
                                                    'label' => __('First visit'),
                                                    'value' => $visitor['first_visit']->diffForHumans(),
                                                    'class' => 'text-gray-500 dark:text-gray-100'
                                                ],
                                                [
                                                    'icon' => 'arrow-path',
                                                    'label' => __('Last visit'),
                                                    'value' => $visitor['last_visit']->diffForHumans(),
                                                    'class' => 'text-gray-500 dark:text-gray-100'
                                                ],
                                                [
                                                    'icon' => 'clock',
                                                    'label' => '',
                                                    'value' => number_format($visitor['session_duration'], 1) . 's',
                                                    'class' => 'font-medium text-gray-700 dark:text-gray-50'
                                                ]
                                            ] as $item)
                                                <div class="text-sm {{ $item['class'] }}">
                                                    <x-icon name="heroicon-o-{{ $item['icon'] }}" class="w-4 h-4 inline mr-1" />
                                                    @if($item['label'])
                                                        {{ $item['label'] }}:
                                                    @endif
                                                    {{ $item['value'] }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach([
                                        [
                                            'label' => 'Total Pages',
                                            'value' => $visitor['total_pages']
                                        ],
                                        [
                                            'label' => 'Total Duration',
                                            'value' => gmdate('H:i:s', $visitor['total_duration'])
                                        ],
                                        [
                                            'label' => 'Time Spent',
                                            'value' => gmdate('H:i:s', $visitor['total_time_spent'])
                                        ],
                                        [
                                            'label' => 'Total Visits',
                                            'value' => $visitor['total_visits']
                                        ]
                                    ] as $stat)
                                        <div class="bg-blue-50 dark:bg-gray-500 rounded-lg p-4">
                                            <span class="text-gray-500 dark:text-gray-100 text-sm block mb-1">{{ __($stat['label']) }}</span>
                                            <p class="text-2xl font-bold text-gray-700 dark:text-gray-50">{{ $stat['value'] }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 bg-blue-50 dark:bg-gray-500 rounded-lg hover:bg-blue-100 dark:hover:bg-gray-400 transition-colors duration-200">
                                    <div class="flex items-center space-x-4">
                                        <span class="text-gray-600 dark:text-gray-50">{{ __('View Pages') }}</span>
                                        <span class="text-sm text-gray-500 dark:text-gray-200">({{ $visitor['total_pages'] }} pages, {{ gmdate('H:i:s', $visitor['total_time_spent']) }} total)</span>
                                    </div>
                                    <template x-if="open">
                                        <x-icon name="heroicon-o-chevron-down" class="w-5 h-5 transform transition-transform duration-200" />
                                    </template>
                                    <template x-if="!open">
                                        <x-icon name="heroicon-o-chevron-up" class="w-5 h-5 transform transition-transform duration-200" />
                                    </template>
                                </button>

                                <div x-show="open" x-collapse class="space-y-4">
                                    @foreach($visitor['pages'] as $page)
                                        <div class="bg-blue-50 dark:bg-gray-500 rounded-lg p-4">
                                            <div class="flex flex-col space-y-4">
                                                <!-- Title and Path -->
                                                <div>
                                                    <h4 class="text-lg font-medium text-gray-700 dark:text-gray-50 truncate" title="{{ is_array($page->title) ? implode(', ', $page->title) : $page->title }}">
                                                        {{ is_array($page->title) ? implode(', ', $page->title) : $page->title }}
                                                    </h4>
                                                    <p class="text-sm text-gray-500 dark:text-gray-100 truncate" title="{{ $page->path }}">
                                                        {{ $page->path }}
                                                    </p>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <!-- Page Info -->
                                                    <div class="space-y-3">
                                                        @foreach([
                                                            [
                                                                'icon' => 'identification',
                                                                'label' => 'ID',
                                                                'value' => $page->id
                                                            ],
                                                            [
                                                                'icon' => 'link',
                                                                'label' => '',
                                                                'value' => $page->page,
                                                                'truncate' => true
                                                            ],
                                                            [
                                                                'icon' => 'clock',
                                                                'label' => 'Created',
                                                                'value' => $page->created_at->format('Y-m-d H:i:s')
                                                            ],
                                                            [
                                                                'icon' => 'arrow-path',
                                                                'label' => 'Updated',
                                                                'value' => $page->updated_at->format('Y-m-d H:i:s')
                                                            ],
                                                            [
                                                                'icon' => 'code-bracket',
                                                                'label' => 'Charset',
                                                                'value' => implode(', ', $page->charset)
                                                            ]
                                                        ] as $info)
                                                            <div class="flex items-center space-x-2">
                                                                <x-icon name="heroicon-o-{{ $info['icon'] }}" class="w-4 h-4 text-blue-300"/>
                                                                <span class="text-sm text-gray-500 dark:text-gray-100 {{ isset($info['truncate']) && $info['truncate'] ? 'truncate' : '' }}"
                                                                    @if(isset($info['truncate']) && $info['truncate']) title="{{ $info['value'] }}" @endif>
                                                                    {{ $info['label'] ? $info['label'] . ': ' : '' }}{{ $info['value'] }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <!-- Analytics -->
                                                    <div class="grid grid-cols-2 gap-3">
                                                        @foreach([
                                                            [
                                                                'icon' => 'clock',
                                                                'label' => 'Duration',
                                                                'value' => gmdate('H:i:s', $page->total_duration_seconds)
                                                            ],
                                                            [
                                                                'icon' => 'clock',
                                                                'label' => 'Time Spent',
                                                                'value' => gmdate('H:i:s', $page->total_time_spent)
                                                            ],
                                                            [
                                                                'icon' => 'clock',
                                                                'label' => 'Avg Time',
                                                                'value' => number_format($page->avg_time_on_page, 2) . 's'
                                                            ],
                                                            [
                                                                'icon' => 'user-group',
                                                                'label' => 'Total Visits',
                                                                'value' => $page->total_visits
                                                            ],
                                                            [
                                                                'icon' => 'arrow-trending-down',
                                                                'label' => 'Bounce Rate',
                                                                'value' => $page->bounce_rate . '%'
                                                            ],
                                                            [
                                                                'icon' => 'chart-bar',
                                                                'label' => 'Engagement',
                                                                'value' => $page->engagement_rate . '%'
                                                            ]
                                                        ] as $metric)
                                                            <div class="bg-white dark:bg-gray-600 p-2 rounded flex">
                                                                <div class="flex items-center space-x-2 flex-1">
                                                                    <x-icon name="heroicon-o-{{ $metric['icon'] }}" class="w-4 h-4 text-blue-300"/>
                                                                    <span class="text-xs text-gray-400 dark:text-gray-100">{{ $metric['label'] }}</span>
                                                                </div>
                                                                <span class="flex-1 text-sm font-medium text-gray-700 dark:text-gray-50 text-right">
                                                                    {{ $metric['value'] }}
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </x-analytics::view>



                    <x-analytics::view view="cards" class="grid grid-cols-1 md:grid-cols-2  gap-4">
                        @foreach($visitors as $visitor)
                            <div x-data="{ open: false }" class="bg-white dark:bg-gray-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-800 p-6">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-full">
                                            <x-icon name="heroicon-o-user-group" class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $visitor['session_id'] }}</h3>
                                            <p class="text-gray-500 dark:text-gray-400 text-sm">ID: {{ $visitor['id'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                            <span class="text-gray-600 dark:text-gray-400 text-sm">{{ __('Total Pages') }}</span>
                                            <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $visitor['total_pages'] }}</p>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                            <span class="text-gray-600 dark:text-gray-400 text-sm">{{ __('Total Visits') }}</span>
                                            <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $visitor['total_visits'] }}</p>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3">
                                        <span class="text-gray-600 dark:text-gray-400 text-sm">{{ __('Session Duration') }}</span>
                                        <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ number_format($visitor['session_duration'], 1) }}s</p>
                                    </div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        <p>{{ __('First Visit') }}: {{ $visitor['first_visit']->diffForHumans() }}</p>
                                        <p>{{ __('Last Visit') }}: {{ $visitor['last_visit']->diffForHumans() }}</p>
                                    </div>

                                    <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <span class="text-gray-700 dark:text-gray-300">{{ __('View Pages') }}</span>
                                        <template x-if="open">
                                            <x-icon name="heroicon-o-chevron-down" class="w-5 h-5 transform transition-transform duration-200" />
                                        </template>
                                        <template x-if="!open">
                                            <x-icon name="heroicon-o-chevron-up" class="w-5 h-5 transform transition-transform duration-200" />
                                        </template>
                                    </button>

                                    <div x-show="open" x-collapse class="space-y-4">
                                        @foreach($visitor['pages'] as $page)
                                            <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ is_array($page->title) ? implode(', ', $page->title) : $page->title }}</h4>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $page->path }}</p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Duration') }}: {{ gmdate('H:i:s', $page->total_duration_seconds) }}</p>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Visits') }}: {{ $page->total_visits }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-analytics::view>

                    <x-analytics::view view="compact" class="overflow-hidden bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ID/Session') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Pages') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Duration') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Visits') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                                @foreach($visitors as $visitor)
                                    <tr x-data="{ isOpen: false }" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $visitor['session_id'] }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $visitor['id'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">
                                            {{ $visitor['total_pages'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">
                                            {{ number_format($visitor['session_duration'], 1) }}s
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900 dark:text-gray-100">
                                            {{ $visitor['total_visits'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <button @click="isOpen = !isOpen" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                                                <template x-if="isOpen">
                                                    <x-icon name="heroicon-o-chevron-up" class="w-5 h-5 transform transition-transform duration-200" />
                                                </template>
                                                <template x-if="!isOpen">
                                                    <x-icon name="heroicon-o-chevron-down" class="w-5 h-5 transform transition-transform duration-200" />
                                                </template>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr x-show="isOpen" x-collapse>
                                        <td colspan="5" class="px-6 py-4 bg-gray-50 dark:bg-gray-800">
                                            <div class="space-y-4">
                                                @foreach($visitor['pages'] as $page)
                                                    <div class="bg-white dark:bg-gray-900 rounded-lg p-4">
                                                        <div class="flex justify-between items-start">
                                                            <div>
                                                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ is_array($page->title) ? implode(', ', $page->title) : $page->title }}</h4>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $page->path }}</p>
                                                            </div>
                                                            <div class="text-right">
                                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Duration') }}: {{ gmdate('H:i:s', $page->total_duration_seconds) }}</p>
                                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Visits') }}: {{ $page->total_visits }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>





                </div>

                <div class="mt-6">
                    <x-analytics::pagination :paginator="$visitors" />
                </div>
            @endif
        </div>
    </div>
</x-site>
