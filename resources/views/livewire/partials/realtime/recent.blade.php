<div class="flex flex-col">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b dark:border-gray-700 bg-gradient-to-r from-indigo-50/50 to-purple-50/50 dark:from-gray-800/50 dark:to-gray-700/50">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="relative flex items-center justify-center w-12 h-12">
                        <div class="absolute inset-0 bg-gradient-to-r from-indigo-500 to-purple-500 opacity-20 rounded-lg animate-pulse"></div>
                        <x-icon name="heroicon-o-bolt" class="w-6 h-6 text-indigo-500 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h2 class="font-extrabold text-xl bg-gradient-to-r from-indigo-500 to-purple-500 bg-clip-text text-transparent">
                            {{ __('Live Activity Feed') }}
                        </h2>
                        @if($isPaused)
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('Paused at') }}: {{ now($site->timezone)->format('M j, Y g:i:s A T') }}
                            </div>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button type="button"
                        wire:click="$toggle('groupSimilarPages')"
                        class="px-4 py-2.5 text-sm font-medium bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900/40 dark:to-purple-900/40 hover:from-indigo-200 hover:to-purple-200 dark:hover:from-indigo-800/40 dark:hover:to-purple-800/40 text-indigo-700 dark:text-indigo-300 rounded-xl shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                        <div class="flex items-center space-x-2">
                            <x-icon name="heroicon-o-squares-2x2" class="w-5 h-5" />
                            <span>{{ $groupSimilarPages ? __('Ungroup Pages') : __('Group Similar') }}</span>
                        </div>
                    </button>
                    <button type="button"
                        wire:click="togglePause"
                        class="px-5 py-2.5 text-sm font-semibold bg-gradient-to-r from-indigo-400 to-purple-400 hover:from-indigo-500 hover:to-purple-500 text-white rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 ease-in-out">
                        <div class="flex items-center space-x-2">
                            <x-icon name="{{ $isPaused ? 'heroicon-o-play-circle' : 'heroicon-o-pause-circle' }}" class="w-5 h-5" />
                            <span>{{ $isPaused ? __('Resume Feed') : __('Pause Feed') }}</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
        @if(count($recent) == 0)
            {{-- Empty state message --}}
            <div class="p-8 flex flex-col items-center justify-center">
                <div class="relative w-24 h-24 mb-6">
                    <div class="absolute inset-0 bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-full animate-pulse"></div>
                    <x-icon name="heroicon-o-chart-bar" class="w-12 h-12 text-indigo-500 dark:text-indigo-400 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2" />
                </div>
                <div class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-400 dark:to-purple-400 bg-clip-text text-transparent mb-3">
                    {{ __('No realtime data available') }}
                </div>
                <p class="text-base text-gray-500 dark:text-gray-400 mb-4 max-w-sm">
                    {{ __('Pageviews will appear here as they happen') }}
                </p>
                <div class="flex items-center gap-2 text-sm text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-800/50 px-4 py-2 rounded-full">
                    <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 animate-spin" />
                    {{ __('Real-time analytics updates every 5 seconds') }}
                </div>
            </div>
        @else
            {{-- List of recent pageviews --}}
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @if($groupSimilarPages)
                    {{-- Grouped view --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 p-4">
                        @foreach($recent->groupBy('page') as $page => $pageviews)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 p-4 relative group">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="bg-indigo-100 dark:bg-indigo-900/50 px-3 py-1.5 rounded-full text-sm font-medium text-indigo-600 dark:text-indigo-400 flex items-center gap-2">
                                        <x-icon name="heroicon-o-eye" class="w-4 h-4" />
                                        {{ $pageviews->count() }} {{ __('views') }}
                                    </div>
                                    <div class="bg-green-100 dark:bg-green-900/50 px-3 py-1.5 rounded-full text-sm font-medium text-green-600 dark:text-green-400 flex items-center gap-2">
                                        <x-icon name="heroicon-o-users" class="w-4 h-4" />
                                        {{ $pageviews->unique('session_id')->count() }} {{ __('visitors') }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="bg-gradient-to-br from-red-100 via-red-50 to-red-100 dark:from-red-900/40 dark:via-red-900/30 dark:to-red-900/20 p-2 rounded-lg">
                                        <img src="https://www.google.com/s2/favicons?domain={{ $site->domain }}"
                                            class="w-5 h-5"
                                            alt="Site favicon">
                                    </div>
                                    <h3 class="font-medium text-gray-900 dark:text-white truncate">
                                        {{ Str::limit($pageviews->first()->page_title ?: $page, 50) }}
                                    </h3>
                                </div>
                                <div class="space-y-2">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-mono bg-gray-50 dark:bg-gray-900 px-2 py-1 rounded truncate block">
                                            {{ $page }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 mt-2">
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Countries') }}:</div>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($pageviews->unique('country')->pluck('country')->filter() as $country)
                                                    <img src="{{ asset('/vendor/analytics/icons/countries/' . formatFlag($country)) }}.svg"
                                                        class="w-6 h-6"
                                                        title="{{ $country }}"
                                                        alt="{{ $country }} flag">
                                                @endforeach
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('Devices') }}:</div>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($pageviews->unique('device')->pluck('device') as $device)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                                        {{ Str::title($device) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 pt-3 border-t dark:border-gray-700">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('First view') }}:</span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $pageviews->min('created_at')->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm mt-1">
                                        <span class="text-gray-500 dark:text-gray-400">{{ __('Last view') }}:</span>
                                        <span class="text-gray-700 dark:text-gray-300">{{ $pageviews->max('created_at')->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    @foreach($recent as $pageview)
                    <div class="p-4 transform hover:scale-[1.01] transition-all duration-200">
                        {{-- Page URL and title --}}
                        <div class="flex items-start justify-between cursor-pointer hover:bg-gray-50/80 dark:hover:bg-gray-800/70 transition-all duration-300 rounded-lg p-3 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl backdrop-blur-sm">
                            <div class="flex items-start gap-4 flex-1">
                                <div class="bg-gradient-to-br from-red-100 via-red-50 to-red-100 dark:from-red-900/40 dark:via-red-900/30 dark:to-red-900/20 p-3 rounded-lg shadow-lg animate-pulse">
                                    <img src="https://www.google.com/s2/favicons?domain={{ $site->domain }}"
                                        class="w-6 h-6 transform hover:rotate-12 transition-transform"
                                        onerror="this.onerror=null; this.src='https://cdn.jsdelivr.net/npm/heroicons/24/outline/globe.svg'"
                                        alt="Site favicon">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3">
                                        <span class="font-semibold text-gray-900 dark:text-white text-lg bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent truncate hover:whitespace-normal hover:text-clip"
                                            title="{{ $site->domain . $pageview->page }}">
                                            {{ Str::limit($pageview->page_title ?: $pageview->page, 200) }}
                                        </span>
                                        <a href="{{ $site->domain . $pageview->page }}"
                                        target="_blank"
                                        @click.stop
                                        rel="nofollow noreferrer noopener"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-all hover:scale-110">
                                            <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-5 h-5" />
                                        </a>
                                    </div>
                                    <a href="{{ $site->domain . $pageview->page }}"
                                       target="_blank"
                                       rel="nofollow noreferrer noopener"
                                       class="block text-sm font-mono bg-gray-50 dark:bg-gray-900 px-3 py-1 rounded-md border border-gray-200 dark:border-gray-700 mt-1 truncate hover:text-clip hover:whitespace-normal group-hover:border-indigo-300 dark:group-hover:border-indigo-700 transition-colors hover:bg-gray-100 dark:hover:bg-gray-800"
                                       title="{{ $site->domain . $pageview->page }}">
                                        <span class="text-indigo-500 dark:text-indigo-400">{{ $site->domain }}</span><span class="text-gray-600 dark:text-gray-300 max-w-[200px] truncate hover:max-w-full transition-all duration-300" title="{{ $pageview->page }}">{{ $pageview->page }}</span>
                                    </a>
                                    <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400 mt-2">
                                        <div class="flex items-center gap-1.5 hover:text-primary-500 transition-colors">
                                            <x-icon name="heroicon-o-clock" class="w-4 h-4" />
                                            <span>{{ $pageview->created_at->diffForHumans(['options' => \Carbon\Carbon::JUST_NOW]) }}</span>
                                        </div>
                                        <span class="text-gray-300 dark:text-gray-600">•</span>
                                        <div class="flex items-center gap-1.5 hover:text-primary-500 transition-colors">
                                            @if($pageview->referrer)
                                                <img src="https://{{ parse_url($pageview->referrer, PHP_URL_HOST) }}/favicon.ico"
                                                    class="w-4 h-4"
                                                    onerror="this.src='{{ asset('vendor/analytics/icons/globe.svg') }}'"
                                                    alt="Referrer favicon">
                                                <span class="max-w-[200px] truncate hover:max-w-full transition-all duration-300" title="{{ $pageview->referrer }}">{{ $pageview->referrer }}</span>
                                            @else
                                                <x-icon name="heroicon-o-arrow-uturn-left" class="w-4 h-4" />
                                                <span>{{ __('Direct visit') }}</span>
                                            @endif
                                        </div>

                                        <span class="text-gray-300 dark:text-gray-600">•</span>
                                        <div class="flex items-center gap-1.5 hover:text-primary-500 transition-colors">
                                            <x-icon name="heroicon-o-calendar" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->created_at->timezone($pageview->timezone ?: 'UTC')->format('M j, Y g:i:s A T') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="bg-green-100 dark:bg-green-900/30 px-3 py-1.5 rounded-lg shadow-inner group hover:bg-green-200 dark:hover:bg-green-900/50 transition-all duration-300">
                                        <div class="flex items-center gap-2">
                                            <x-icon name="heroicon-o-users" class="w-4 h-4 text-green-700 dark:text-green-400" />
                                            <span class="font-semibold text-green-700 dark:text-green-400">
                                                {{ $pageview->current_visitors }}
                                                <span class="text-sm font-normal">{{ __('active now') }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
                            {{-- Location info --}}
                            <div class="flex items-start gap-3 bg-blue-50/50 dark:bg-blue-900/20 p-3 rounded-lg hover:shadow-lg transition-shadow duration-300 hover:bg-blue-100/50 dark:hover:bg-blue-900/30">
                                <div class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg shadow-inner">
                                    <img src="{{ asset('/vendor/analytics/icons/countries/' . formatFlag($pageview->country)) }}.svg"
                                        class="w-6 h-6 hover:scale-110 transition-transform"
                                        alt="Country flag">
                                </div>
                                <div class="min-w-0 space-y-2">
                                    @if($pageview->country)
                                        <span class="block font-medium text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                            <x-icon name="heroicon-o-globe-alt" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->country }}
                                        </span>
                                    @endif
                                    @if($pageview->city)
                                        <span class="block text-sm text-gray-600 dark:text-gray-400 hover:text-blue-500 transition-colors">
                                            <x-icon name="heroicon-o-map-pin" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->city }}
                                        </span>
                                    @endif
                                    @if($pageview->ip)
                                        <span class="block text-sm text-gray-500 hover:text-blue-500 transition-colors">
                                            <x-icon name="heroicon-o-server" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->ip }}
                                        </span>
                                    @endif
                                    @if($pageview->timezone)
                                        <span class="block text-sm text-gray-500 hover:text-blue-500 transition-colors">
                                            <x-icon name="heroicon-o-clock" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->timezone }}
                                        </span>
                                    @endif
                                    @if($pageview->language)
                                        <span class="block text-sm text-gray-500 hover:text-blue-500 transition-colors">
                                            <x-icon name="heroicon-o-language" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->language }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Browser info --}}
                            <div class="flex items-start gap-3 bg-purple-50/50 dark:bg-purple-900/20 p-3 rounded-lg hover:shadow-lg transition-shadow duration-300 hover:bg-purple-100/50 dark:hover:bg-purple-900/30">
                                <div class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-lg shadow-inner">
                                    <img src="{{ asset('/vendor/analytics/icons/browsers/'.formatBrowser($pageview->browser)) }}.svg"
                                        class="w-6 h-6 hover:scale-110 transition-transform"
                                        alt="Browser icon">
                                </div>
                                <div class="flex-1 min-w-0 space-y-2">
                                    <span class="block font-medium text-gray-900 dark:text-gray-100 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                        <x-icon name="heroicon-o-globe-alt" class="w-4 h-4 inline mr-1" />
                                        {{ $pageview->browser ?: __('Unknown') }}
                                    </span>
                                    @if($pageview->browser_version)
                                        <span class="block text-sm text-gray-500 hover:text-purple-500 transition-colors">
                                            <x-icon name="heroicon-o-code-bracket" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->browser_version }}
                                        </span>
                                    @endif
                                    <span class="block text-sm text-gray-600 dark:text-gray-400 hover:text-purple-500 transition-colors">
                                        <x-icon name="heroicon-o-computer-desktop" class="w-4 h-4 inline mr-1" />
                                        {{ $pageview->os ?: __('Unknown') }}
                                    </span>
                                    <span class="block text-sm text-gray-500 hover:text-purple-500 transition-colors">
                                        <x-icon name="heroicon-o-device-phone-mobile" class="w-4 h-4 inline mr-1" />
                                        {{ Str::title($pageview->device) ?: __('Unknown device') }}
                                    </span>
                                    @if($pageview->user_agent)
                                        <span class="block text-sm text-gray-500 hover:text-purple-500 transition-colors truncate hover:whitespace-normal">
                                            <x-icon name="heroicon-o-information-circle" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->user_agent }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Source info --}}
                            <div class="flex items-start gap-3 bg-green-50/50 dark:bg-green-900/20 p-3 rounded-lg hover:shadow-lg transition-shadow duration-300 hover:bg-green-100/50 dark:hover:bg-green-900/30">
                                <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg shadow-inner">
                                    <x-icon name="heroicon-o-arrow-trending-up" class="w-6 h-6 text-green-600 dark:text-green-400" />
                                </div>
                                <div class="flex-1 min-w-0 space-y-2">
                                    @if($pageview->url_path)
                                        <span class="block text-sm text-gray-600 dark:text-gray-400 hover:text-green-500 transition-colors truncate hover:whitespace-normal">
                                            <x-icon name="heroicon-o-map" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->url_path }}
                                        </span>
                                    @endif

                                    @if($pageview->url_query)
                                        <span class="block text-sm text-gray-500 hover:text-green-500 transition-colors truncate hover:whitespace-normal">
                                            <x-icon name="heroicon-o-question-mark-circle" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->url_query }}
                                        </span>
                                    @endif

                                    @if($pageview->session_id)
                                        <span class="block text-sm text-gray-500 hover:text-green-500 transition-colors truncate hover:whitespace-normal">
                                            <x-icon name="heroicon-o-finger-print" class="w-4 h-4 inline mr-1" />
                                            {{ $pageview->session_id }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif

                <div class="mt-4 p-4">
                    @if($recent instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        {{ $recent->links() }}
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
