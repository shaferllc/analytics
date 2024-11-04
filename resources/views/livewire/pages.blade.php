<div>
    <x-analytics::title
        :title="__('Real')"
        :totalPageviews="$total"
        :icon="'heroicon-o-document-text'"
        :totalText="__('Total Pageviews')"
        :data="$data"
        :total="$total" 
        :first="$first" 
        :last="$last" 
        :website="$website" 
        :dateRange="$dateRange"
        :page="$page"
        :perPage="$perPage"
        :sortBy="$sortBy"
        :sort="$sort"
        :from="$from"
        :to="$to"
        :search="$search"
    />

    <!-- Pages list -->
    <div>
        @if(count($data) == 0)
            <x-analytics::no-results />
        @else
            <div class="space-y-4">
                <div class="flex items-center justify-between text-lg font-bold text-gray-800 dark:text-gray-100 mt-6 bg-gradient-to-r from-blue-200 to-purple-200 dark:from-blue-800 dark:to-purple-800 p-5 rounded-xl shadow-lg border border-blue-300 dark:border-purple-700">
                    <div class="flex-grow">
                        <span class="inline-flex items-center text-2xl transition-all duration-300 ease-in-out hover:text-blue-600 dark:hover:text-blue-400">
                            <x-icon name="heroicon-o-link" class="w-7 h-7 mr-4 text-blue-600 dark:text-blue-400 animate-pulse" />
                            {{ __('URL') }}
                        </span>
                    </div>
                    <div>
                        <span class="inline-flex items-center text-2xl transition-all duration-300 ease-in-out hover:text-purple-600 dark:hover:text-purple-400">
                            <x-icon name="heroicon-o-eye" class="w-7 h-7 mr-4 text-purple-600 dark:text-purple-400 animate-pulse" />
                            {{ __('Pageviews') }}
                        </span>
                    </div>
                </div>

                @foreach($data as $page)
                    <div class="group hover:bg-gradient-to-r from-blue-50 to-purple-50 dark:hover:bg-gradient-to-r dark:from-gray-700 dark:to-gray-600 rounded-lg transition duration-300 ease-in-out p-3 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center truncate space-x-2 flex-grow">
                                <div class="bg-blue-100 dark:bg-blue-800 p-1 rounded-full animate-pulse">
                                    <x-icon name="heroicon-o-document-text" class="w-4 h-4 text-blue-500 dark:text-blue-300" />
                                </div>
                                <div class="flex flex-col">
                                    <div class="truncate text-gray-900 dark:text-gray-100 font-medium text-base" dir="ltr">{{ $page->value }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $page->page_title }}</div>
                                </div>
                                <a href="http://{{ $website->domain . $page->value }}" target="_blank" rel="nofollow noreferrer noopener" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition duration-300 ease-in-out transform hover:scale-110">
                                    <x-icon name="heroicon-o-link" class="w-4 h-4 inline" />
                                </a>
                            </div>
                            <div class="text-right text-white font-bold bg-gradient-to-r from-blue-400 to-purple-500 px-3 py-1 rounded-full shadow-inner hover:shadow-md transition-all duration-300">
                                <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />
                                {{ number_format($page->count, 0, __('.'), __(',')) }}
                            </div>
                        </div>
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full h-3 transition-all duration-500 ease-in-out relative" style="width: {{ ($page->count / $total) * 100 }}%">
                                <div class="absolute inset-0 bg-white opacity-25 animate-pulse"></div>
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-right text-gray-500 dark:text-gray-400">
                            {{ number_format(($page->count / $total) * 100, 2) }}% of total pageviews
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
            <x-analytics::pagination :data="$data" />
        @endif
    </div>
</div>
