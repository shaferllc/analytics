<x-modal show="showModal" :title="__('Page Details')" icon="heroicon-o-information-circle">
    <div class="space-y-8" x-data="{ activeTab: 'urls' }">
        <!-- Tabs Navigation -->
        <div class="border-b border-gray-800">
            <nav class="-mb-px flex space-x-6">
                <button 
                    @click="activeTab = 'urls'"
                    :class="{'border-b-2 border-indigo-400 text-indigo-400': activeTab === 'urls',
                            'text-gray-400 hover:text-gray-300': activeTab !== 'urls'}"
                    class="py-2 px-1 font-medium text-sm flex items-center gap-2"
                >
                    <x-icon name="heroicon-o-link" class="w-5 h-5" />
                    {{ __('URLs') }}
                </button>

                <button 
                    @click="activeTab = 'titles'"
                    :class="{'border-b-2 border-indigo-400 text-indigo-400': activeTab === 'titles',
                            'text-gray-400 hover:text-gray-300': activeTab !== 'titles'}"
                    class="py-2 px-1 font-medium text-sm flex items-center gap-2"
                >
                    <x-icon name="heroicon-o-document-text" class="w-5 h-5" />
                    {{ __('Titles') }}
                </button>

                @if(isset($pageData->query))
                <button 
                    @click="activeTab = 'query'"
                    :class="{'border-b-2 border-indigo-400 text-indigo-400': activeTab === 'query',
                            'text-gray-400 hover:text-gray-300': activeTab !== 'query'}"
                    class="py-2 px-1 font-medium text-sm flex items-center gap-2"
                >
                    <x-icon name="heroicon-o-question-mark-circle" class="w-5 h-5" />
                    {{ __('Query Parameters') }}
                </button>
                @endif

                @if(isset($pageData->campaign_metrics))
                <button 
                    @click="activeTab = 'campaign'"
                    :class="{'border-b-2 border-indigo-400 text-indigo-400': activeTab === 'campaign',
                            'text-gray-400 hover:text-gray-300': activeTab !== 'campaign'}"
                    class="py-2 px-1 font-medium text-sm flex items-center gap-2"
                >
                    <x-icon name="heroicon-o-megaphone" class="w-5 h-5" />
                    {{ __('Campaign') }}
                </button>
                @endif

                @if(isset($pageData->landing_page))
                <button 
                    @click="activeTab = 'landing'"
                    :class="{'border-b-2 border-indigo-400 text-indigo-400': activeTab === 'landing',
                            'text-gray-400 hover:text-gray-300': activeTab !== 'landing'}"
                    class="py-2 px-1 font-medium text-sm flex items-center gap-2"
                >
                    <x-icon name="heroicon-o-home" class="w-5 h-5" />
                    {{ __('Landing Page') }}
                </button>
                @endif
            </nav>
        </div>

        <!-- Tab Panels -->
        <div class="mt-4">
            <!-- URLs Tab -->
            <div x-show="activeTab === 'urls'" class="space-y-4">
                <div class="flex items-center gap-3 mb-6 text-sm text-indigo-200 bg-indigo-900/20 p-3 rounded-lg">
                    <x-icon name="heroicon-o-link" class="w-5 h-5 text-indigo-400 flex-shrink-0" />
                    <span>{{ __('Full URLs of pages visited by users') }}</span>
                </div>
                @if(empty($pageData->url))
                    <div class="text-gray-400 text-sm bg-gray-800/30 p-4 rounded-lg text-center">{{ __('No URL data available yet') }}</div>
                @else
                    @foreach($pageData->url as $url)
                        <div class="flex items-center justify-between bg-gray-800/70 hover:bg-gray-800/90 transition p-4 rounded-lg gap-4">
                            <code class="text-sm text-indigo-300 break-all flex-1 font-mono">{{ $url['value'] }}</code>
                            <span class="text-sm font-medium text-gray-300 bg-gray-700/70 px-3 py-1.5 rounded-full whitespace-nowrap" title="{{ __('Number of page views') }}">
                                {{ number_format($url['count']) }} {{ __('views') }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Titles Tab -->
            <div x-show="activeTab === 'titles'" class="space-y-4">
                <div class="flex items-center gap-3 mb-6 text-sm text-indigo-200 bg-indigo-900/20 p-3 rounded-lg">
                    <x-icon name="heroicon-o-document-text" class="w-5 h-5 text-indigo-400 flex-shrink-0" />
                    <div>
                        <span>{{ __('Page titles from HTML document headers') }}</span>
                        <span class="text-xs text-indigo-300 block mt-0.5">({{ __('from <title> tag') }})</span>
                    </div>
                </div>
                @if(empty($pageData->title))
                    <div class="text-gray-400 text-sm bg-gray-800/30 p-4 rounded-lg text-center">{{ __('No title data available yet') }}</div>
                @else
                    @foreach($pageData->title as $title)
                        <div class="flex items-center justify-between bg-gray-800/70 hover:bg-gray-800/90 transition p-4 rounded-lg gap-4">
                            <span class="text-sm text-gray-200 flex-1">{{ $title['value'] }}</span>
                            <span class="text-sm font-medium text-gray-300 bg-gray-700/70 px-3 py-1.5 rounded-full whitespace-nowrap" title="{{ __('Number of occurrences') }}">
                                {{ number_format($title['count']) }} {{ __('times') }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Query Parameters Tab -->
            @if(isset($pageData->query))
            <div x-show="activeTab === 'query'" class="space-y-4">
                <div class="flex items-center gap-3 mb-6 text-sm text-indigo-200 bg-indigo-900/20 p-3 rounded-lg">
                    <x-icon name="heroicon-o-question-mark-circle" class="w-5 h-5 text-indigo-400 flex-shrink-0" />
                    <div>
                        <span>{{ __('URL query parameters and their values') }}</span>
                        <span class="text-xs text-indigo-300 block mt-0.5">({{ __('everything after ? in URLs') }})</span>
                    </div>
                </div>
                @if(empty($pageData->query))
                    <div class="text-gray-400 text-sm bg-gray-800/30 p-4 rounded-lg text-center">{{ __('No query parameters found in URLs') }}</div>
                @else
                    @foreach($pageData->query as $query)
                        <div class="bg-gray-800/70 hover:bg-gray-800/90 transition p-4 rounded-lg">
                            <div class="flex items-center justify-between gap-4">
                                <code class="text-sm text-indigo-300 break-all flex-1 font-mono">{{ $query['value'] }}</code>
                                <span class="text-sm font-medium text-gray-300 bg-gray-700/70 px-3 py-1.5 rounded-full whitespace-nowrap" title="{{ __('Frequency of this query string') }}">
                                    {{ number_format($query['count']) }} {{ __('times') }}
                                </span>
                            </div>
                            @if(isset($query['params']))
                                <div class="mt-4 grid grid-cols-2 gap-3 pl-4 border-l-2 border-indigo-800/50">
                                    @foreach($query['params'] as $param => $value)
                                        <div class="bg-gray-900/50 hover:bg-gray-900/70 transition p-3 rounded-lg" title="{{ __('Parameter value') }}">
                                            <span class="text-xs font-medium text-indigo-400">{{ $param }}</span>
                                            <div class="text-sm text-gray-300 mt-1.5 break-all">{{ $value }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
            @endif

            <!-- Campaign Tab -->
            @if(isset($pageData->campaign_metrics))
            <div x-show="activeTab === 'campaign'">
                <div class="flex items-center gap-3 mb-6 text-sm text-indigo-200 bg-indigo-900/20 p-3 rounded-lg">
                    <x-icon name="heroicon-o-megaphone" class="w-5 h-5 text-indigo-400 flex-shrink-0" />
                    <div>
                        <span>{{ __('Marketing campaign tracking metrics') }}</span>
                        <span class="text-xs text-indigo-300 block mt-0.5">({{ __('UTM parameters and ad tracking') }})</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-800/70 hover:bg-gray-800/90 transition p-4 rounded-lg text-center" title="{{ __('Presence of UTM tracking parameters') }}">
                        <div class="text-sm text-gray-300 mb-2">{{ __('UTM Parameters') }}</div>
                        <div class="text-xl font-semibold text-indigo-400">
                            {{ $pageData->campaign_metrics['has_utm_params'] ? __('Yes') : __('No') }}
                        </div>
                        <div class="text-xs text-gray-400 mt-2">{{ __('Marketing campaign tracking tags') }}</div>
                    </div>
                    <div class="bg-gray-800/70 hover:bg-gray-800/90 transition p-4 rounded-lg text-center" title="{{ __('Campaign tracking completeness score') }}">
                        <div class="text-sm text-gray-300 mb-2">{{ __('Tracking Score') }}</div>
                        <div class="text-xl font-semibold text-indigo-400">
                            {{ $pageData->campaign_metrics['tracking_completeness'] }}%
                        </div>
                        <div class="text-xs text-gray-400 mt-2">{{ __('How well campaigns are tracked') }}</div>
                    </div>
                    <div class="bg-gray-800/70 hover:bg-gray-800/90 transition p-4 rounded-lg text-center" title="{{ __('Paid vs organic traffic source') }}">
                        <div class="text-sm text-gray-300 mb-2">{{ __('Traffic Type') }}</div>
                        <div class="text-xl font-semibold text-indigo-400">
                            {{ $pageData->campaign_metrics['is_paid_traffic'] ? __('Paid') : __('Organic') }}
                        </div>
                        <div class="text-xs text-gray-400 mt-2">{{ __('Paid ads vs natural traffic') }}</div>
                    </div>
                </div>

                @if(isset($pageData->campaign_data))
                    <div class="space-y-4">
                        @foreach($pageData->campaign_data as $param => $data)
                            <div class="bg-gray-800/70 hover:bg-gray-800/90 transition p-4 rounded-lg">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm font-semibold text-indigo-400">{{ strtoupper($param) }}</div>
                                            <div class="text-sm text-gray-300 bg-gray-700/70 px-3 py-1.5 rounded-full ml-2 whitespace-nowrap" title="{{ __('Number of hits with this parameter') }}">
                                                {{ number_format($data['count']) }} {{ __('hits') }}
                                            </div>
                                        </div>
                                        <div class="text-gray-200 mt-2 break-all font-mono">{{ $data['value'] }}</div>
                                        <div class="text-sm text-gray-400 mt-2">{{ $data['description'] }}</div>
                                        @if($data['url'])
                                            <a href="{{ $data['url'] }}" target="_blank" class="inline-flex items-center text-sm text-indigo-400 hover:text-indigo-300 mt-3 transition" title="{{ __('View parameter source') }}">
                                                <span>{{ __('View Source') }}</span>
                                                <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-4 h-4 ml-1.5" />
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-gray-400 text-sm bg-gray-800/30 p-4 rounded-lg text-center">{{ __('No campaign tracking data available') }}</div>
                @endif
            </div>
            @endif

            <!-- Landing Page Tab -->
            @if(isset($pageData->landing_page))
            <div x-show="activeTab === 'landing'" class="space-y-4">
                <div class="flex items-center gap-3 mb-6 text-sm text-indigo-200 bg-indigo-900/20 p-3 rounded-lg">
                    <x-icon name="heroicon-o-home" class="w-5 h-5 text-indigo-400 flex-shrink-0" />
                    <div>
                        <span>{{ __('Entry points where users first arrived') }}</span>
                        <span class="text-xs text-indigo-300 block mt-0.5">({{ __('first page in user sessions') }})</span>
                    </div>
                </div>
                @if(empty($pageData->landing_page))
                    <div class="text-gray-400 text-sm bg-gray-800/30 p-4 rounded-lg text-center">{{ __('No landing page data recorded yet') }}</div>
                @else
                    @foreach($pageData->landing_page as $landing)
                        <div class="flex items-center justify-between bg-gray-800/70 hover:bg-gray-800/90 transition p-4 rounded-lg gap-4">
                            <span class="text-sm text-gray-200 break-all flex-1">{{ $landing['value'] }}</span>
                            <span class="text-sm font-medium text-gray-300 bg-gray-700/70 px-3 py-1.5 rounded-full whitespace-nowrap" title="{{ __('Number of initial visits') }}">
                                {{ number_format($landing['count']) }} {{ __('visits') }}
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
            @endif
        </div>
    </div>
    

    <div class="mt-8 sm:flex sm:flex-ro w-reverse gap-3">
        <x-forms.cancel label="{{ __('Close') }}" @click="showModal = false" />
    </div>
</x-modal>