<div x-show="activeTab === 'outbound_links'">
    <!-- Outbound link analytics provide insights into how users navigate away from your site -->
    <div class="mb-6 p-4 bg-indigo-900/30 border border-indigo-800 rounded-lg">
        <h3 class="text-lg font-medium text-indigo-100 mb-2">{{ __('Outbound Link Insights') }}</h3>
        <p class="text-indigo-300">{{ __('Analyze which external links users click to leave your site. Understand user exit points and identify opportunities to improve engagement and retention.') }}</p>
        
        <div class="mt-4 grid grid-cols-2 gap-8">
            <div class="flex items-center gap-4">
                <div class="w-32 h-24 relative bg-indigo-800/50 border border-indigo-700 rounded-lg shadow-inner">
                    <div class="absolute inset-2 border-2 border-dashed border-indigo-500/50"></div>
                    <div class="absolute top-2 right-2">
                        <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-5 h-5 text-indigo-400" />
                    </div>
                    <div class="absolute bottom-2 left-2 text-sm text-indigo-200">
                        {{ __('External Link') }}
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-sm text-indigo-200">
                        <span class="font-medium text-indigo-400">{{ __('URL') }}:</span> {{ __('Destination URL') }}
                    </p>
                    <p class="text-sm text-indigo-200">
                        <span class="font-medium text-indigo-400">{{ __('Anchor') }}:</span> {{ __('Link text') }}
                    </p>
                    <p class="text-sm text-indigo-200">
                        <span class="font-medium text-indigo-400">{{ __('Domain') }}:</span> {{ __('Target hostname') }}
                    </p>
                </div>
            </div>
            <div>
                <ul class="space-y-3 text-sm text-indigo-200">
                    <li class="flex items-start gap-2">
                        <x-icon name="heroicon-o-link" class="w-5 h-5 text-indigo-400 mt-0.5" />
                        <span>{{ __('Discover the most frequently clicked external links to understand user interests and behavior') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-icon name="heroicon-o-funnel" class="w-5 h-5 text-indigo-400 mt-0.5" />
                        <span>{{ __('Identify common exit points where users leave your site to optimize content and user flows') }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 text-indigo-400 mt-0.5" />
                        <span>{{ __('Measure and compare the performance of different outbound links to focus efforts') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4 lg:grid-cols-3 xl:grid-cols-4 mb-6">
        @foreach($outboundLinks['data'] as $linkData)
            <div x-data="{ open: false }" class="relative">
                <!-- Link Card -->
                <div @click="open = true" class="block p-4 h-full bg-indigo-900/50 border border-indigo-800 rounded-lg shadow-md cursor-pointer transition duration-300 ease-in-out hover:bg-indigo-900/70"
                        title="{{ __('Click for detailed link analytics') }}">
                    <!-- Top Row -->
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full" style="background-color: {{ collect(['#f87171', '#fbbf24', '#34d399'])->random() }};"></div>
                            <span class="text-indigo-100 font-medium truncate max-w-[10rem]">{{ $linkData->hostname }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-xs bg-indigo-700/50 text-indigo-200" 
                                    title="{{ __('Percentage of total outbound link clicks') }}">
                                {{ number_format($linkData->aggregates['total_count'] > 0 ? ($linkData->count / $linkData->aggregates['total_count']) * 100 : 0, 1) }}%
                            </span>
                            <x-icon name="heroicon-o-chevron-right" class="w-5 h-5 text-indigo-400" />
                        </div>
                    </div>

                    <!-- Link Text -->
                    <p class="mb-3 text-sm text-indigo-300 line-clamp-2">
                        {{ $linkData->text }}
                    </p>

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div class="flex items-center gap-1.5 text-indigo-400" title="{{ __('Total clicks') }}">
                            <x-icon name="heroicon-o-cursor-arrow-rays" class="w-4 h-4" />
                            <span>{{ number_format($linkData->count) }}</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-indigo-400" title="{{ __('Unique sessions') }}">
                            <x-icon name="heroicon-o-user-group" class="w-4 h-4" />
                            <span>{{ number_format($linkData->unique_sessions) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Link Details Overlay -->
                <div x-show="open" 
                        x-transition
                        x-cloak
                        @click.away="open = false"
                        class="fixed inset-0 z-50 flex p-8 items-start justify-center overflow-y-auto bg-indigo-950/90 sm:items-center">
                    <div class="w-full max-w-2xl bg-indigo-900 rounded-lg shadow-2xl">
                        <div class="p-6">
                            <!-- Header -->
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-xl font-semibold text-indigo-100">{{ $linkData->hostname }}</h3>
                                    <a href="{{ $linkData->url }}" target="_blank" class="text-sm text-indigo-400 hover:text-indigo-300">{{ $linkData->url }}</a>
                                </div>
                                <button @click="open = false" class="text-indigo-400 hover:text-indigo-300" title="{{ __('Close details') }}">
                                    <x-icon name="heroicon-o-x-mark" class="w-6 h-6" />
                                </button>
                            </div>

                            <!-- Two Column Grid -->
                            <div class="grid grid-cols-2 gap-6">
                                <!-- Link Info -->
                                <div class="space-y-4">
                                    <div class="p-4 bg-indigo-800/50 rounded-lg shadow">
                                        <h4 class="text-sm font-medium text-indigo-300 mb-2">{{ __('Link Information') }}</h4>
                                        <dl class="text-sm space-y-2">
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('URL') }}</dt>
                                                <dd class="text-indigo-200 truncate">{{ $linkData->url }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Anchor') }}</dt>
                                                <dd class="text-indigo-200 truncate">{{ $linkData->text }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Domain') }}</dt>
                                                <dd class="text-indigo-200 truncate">{{ $linkData->hostname }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Protocol') }}</dt>
                                                <dd class="text-indigo-200">{{ $linkData->protocol }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Path') }}</dt>
                                                <dd class="text-indigo-200 truncate">{{ $linkData->pathname }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Hash') }}</dt>
                                                <dd class="text-indigo-200">{{ $linkData->hash }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Query') }}</dt>
                                                <dd class="text-indigo-200 truncate">{{ $linkData->search }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Port') }}</dt>
                                                <dd class="text-indigo-200">{{ $linkData->port }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Target') }}</dt>
                                                <dd class="text-indigo-200">{{ $linkData->target }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Rel') }}</dt>
                                                <dd class="text-indigo-200">{{ $linkData->rel }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Title') }}</dt>
                                                <dd class="text-indigo-200 truncate">{{ $linkData->title }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Page') }}</dt>
                                                <dd class="text-indigo-200 truncate">{{ $linkData->pageTitle }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Source') }}</dt>
                                                <dd class="text-indigo-200 truncate">{{ $linkData->pageUrl }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Timestamp') }}</dt>
                                                <dd class="text-indigo-200">{{ $linkData->timestamp }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>

                                <!-- Click Metrics -->
                                <div class="space-y-4">
                                    <div class="p-4 bg-indigo-800/50 rounded-lg shadow">
                                        <h4 class="text-sm font-medium text-indigo-300 mb-2">{{ __('Click Metrics') }}</h4>
                                        <dl class="text-sm space-y-2">
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Total Clicks') }}</dt>
                                                <dd class="text-indigo-200">{{ number_format($linkData->count) }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Unique Sessions') }}</dt>
                                                <dd class="text-indigo-200">{{ number_format($linkData->unique_sessions) }}</dd>
                                            </div>
                                            <div class="flex justify-between">
                                                <dt class="text-indigo-400">{{ __('Click Share') }}</dt>
                                                <dd class="text-indigo-200">{{ number_format($linkData->aggregates['total_count'] > 0 ? ($linkData->count / $linkData->aggregates['total_count']) * 100 : 0, 1) }}%</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
</div>