<div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-blue-800 text-white">
    <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @include('livewire.partials.tabs')
        @include('livewire.websites.header')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-500 to-purple-600">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                    </svg>
                    {{ __('Browsers') }}
                    <span class="ml-2 text-sm opacity-75">({{ number_format($totalVisitors) }} total)</span>
                </h3>
            </div>
            <div class="p-4">
                @if(count($browsers) == 0)
                    <p class="text-gray-500 dark:text-gray-400 text-center italic">{{ __('No data available') }}.</p>
                @else
                    <div class="space-y-4">
                        <div class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <div class="flex-grow pl-2">{{ __('Browser') }}</div>
                            <div class="flex items-center">
                                <x-icon name="heroicon-o-users" class="w-4 h-4 mr-1" />
                                {{ __('Visitors') }}
                            </div>
                        </div>

                        @foreach($browsers as $browser)
                            <div class="group hover:bg-gradient-to-r from-blue-50 to-purple-50 dark:hover:bg-gradient-to-r dark:from-gray-700 dark:to-gray-600 rounded-lg transition-all duration-200 p-2.5 shadow hover:shadow-lg">
                                <div class="flex justify-between items-center mb-1.5">
                                    <div class="flex items-center truncate space-x-2 flex-grow">
                                        <div class="relative">
                                            <img src="{{ asset('/vendor/analytics/icons/browsers/'.formatBrowser($browser->value)) }}.svg" class="w-5 h-5 rounded-full shadow-sm">
                                            <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                        </div>
                                        <div class="truncate text-gray-900 dark:text-gray-100 font-medium">
                                            {{ $browser->value ?: __('Unknown') }}
                                        </div>
                                    </div>
                                    <div class="text-right text-white text-sm font-bold bg-gradient-to-r from-blue-500 to-purple-500 px-2.5 py-0.5 rounded-full shadow-sm">
                                        {{ number_format($browser->count, 0, __('.'), __(',')) }}
                                    </div>
                                </div>
                                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 rounded-full h-2 transition-all duration-500 ease-out relative" style="width: {{ $totalVisitors > 0 ? ($browser->count / $totalVisitors) * 100 : 0 }}%">
                                        <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
                                    </div>
                                </div>
                                <div class="mt-0.5 text-xs text-right text-gray-500 dark:text-gray-400 font-medium">
                                    {{ $totalVisitors > 0 ? number_format(($browser->count / $totalVisitors) * 100, 1) : 0 }}% of traffic
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if(count($browsers) > 0)
                <div class="p-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
                    <a href="{{ route('websites.analytics.browsers', ['website' => $website, 'from' => $range['from'], 'to' => $range['to']]) }}" class="flex items-center justify-center text-white hover:text-yellow-200 font-bold text-lg transition-colors group">
                        <span class="mr-3 uppercase tracking-wider">{{ __('View all :count Browsers', ['count' => $totalVisitors]) }}</span>
                        <svg class="w-6 h-6 fill-current transform group-hover:translate-x-1 transition-transform duration-200" viewBox="0 0 24 24">
                            <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>