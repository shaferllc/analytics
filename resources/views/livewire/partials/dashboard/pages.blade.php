
<div class="col-span-2">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-500 to-purple-600">
            <h3 class="text-xl font-bold text-white flex items-center">
                <x-icon name="heroicon-o-document-text" class="w-6 h-6 mr-2" />
                {{ __('Pages') }}
                <span class="ml-2 text-sm opacity-75">({{ number_format($totalPages) }} total)</span>
            </h3>
        </div>
        <div class="p-4">
            @if(count($pages) == 0)
                <p class="text-gray-500 dark:text-gray-400 text-center italic">{{ __('No data available') }}.</p>
            @else
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-4 text-sm font-medium text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 pb-3">
                        <div class="flex items-center space-x-3 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/30 dark:to-purple-900/30 p-2 rounded-lg">
                            <x-icon name="heroicon-o-link" class="w-5 h-5 text-blue-500 dark:text-blue-400" />
                            <span class="text-gray-700 dark:text-gray-300">{{ __('URL') }}</span>
                        </div>
                        <div class="flex items-center justify-end space-x-3 bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/30 dark:to-purple-900/30 p-2 rounded-lg">
                            <x-icon name="heroicon-o-eye" class="w-5 h-5 text-purple-500 dark:text-purple-400" />
                            <span class="text-gray-700 dark:text-gray-300">{{ __('Pageviews') }}</span>
                        </div>
                    </div>

                    @foreach($pages as $page)
                        <div class="group hover:bg-gradient-to-r from-blue-50 to-purple-50 dark:hover:bg-gradient-to-r dark:from-gray-700 dark:to-gray-600 rounded-lg transition-all duration-200 p-2.5 shadow hover:shadow-lg">
                            <div class="flex justify-between items-center mb-1.5">
                                <div class="flex items-center truncate space-x-2 flex-grow">
                                    <div class="bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-800 dark:to-purple-800 p-1 rounded-full">
                                        <x-icon name="heroicon-o-document-text" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-300" />
                                    </div>
                                    <div class="truncate text-gray-900 dark:text-gray-100 font-medium text-sm" dir="ltr">{{ $page->value }}</div>
                                    <a href="http://{{ $site->domain . $page->value }}" target="_blank" rel="nofollow noreferrer noopener" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                        <x-icon name="heroicon-o-link" class="w-3.5 h-3.5" />
                                    </a>
                                </div>
                                <div class="text-right text-white text-sm font-bold bg-gradient-to-r from-blue-500 to-purple-500 px-2.5 py-0.5 rounded-full shadow-sm">
                                    {{ Number::format($page->count) }}
                                </div>
                            </div>
                            <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 rounded-full h-2 transition-all duration-500 ease-out relative" style="width: {{ $totalPageviews > 0 ? ($page->count / $totalPageviews) * 100 : 0 }}%">
                                    <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
                                </div>
                            </div>
                            <div class="mt-0.5 text-xs text-right text-gray-500 dark:text-gray-400 font-medium">
                                @dump($totalPageviews)
                                {{ $totalPageviews > 0 ? number_format(($page->count / $totalPageviews) * 100, 1) : 0 }}% of traffic
                            </div>
                            <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                            <div class="bg-green-100 dark:bg-green-800 p-2 rounded-lg">
                                <span class="font-semibold">Avg. Time on Page:</span>
                                <span>{{ gmdate("H:i:s", $page->averageTimeOnPage($page->value) ?? 0) }}</span>
                            </div>
                            <div class="bg-yellow-100 dark:bg-yellow-800 p-2 rounded-lg">
                                <span class="font-semibold">Bounce Rate:</span>
                                <span>{{ number_format($page->getBounceRatePerPage($page->value)->first() ?? 0, 2) . '%' }}</span>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-800 p-2 rounded-lg">
                                <span class="font-semibold">Unique Visitors:</span>
                                <span>{{ number_format($page->getUniqueVisitors($page->value)) }}</span>
                            </div>
                            <div class="bg-red-100 dark:bg-red-800 p-2 rounded-lg">
                                <span class="font-semibold">Exit Rate:</span>
                                <span>{{ number_format($page->getExitRate($page->value), 2) . '%' }}</span>
                            </div>
                        </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        @if(count($pages) > 0)
            <div class="p-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
                <a href="{{ route('websites.analytics.pages', ['website' => $site, 'from' => $range['from'], 'to' => $range['to']]) }}" class="flex items-center justify-center text-white hover:text-yellow-200 font-bold text-lg transition-colors group">
                    <span class="mr-3 uppercase tracking-wider">{{ __('View all :count Pages', ['count' => $totalPages]) }}</span>
                    <svg class="w-6 h-6 fill-current transform group-hover:translate-x-1 transition-transform duration-200" viewBox="0 0 24 24">
                        <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</div>
