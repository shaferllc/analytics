<div class="col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-all duration-300 hover:shadow-xl">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-500 to-purple-600">
                <h3 class="text-xl font-bold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    {{ __('Referrers') }}
                    <span class="ml-2 text-sm opacity-75">({{ number_format($totalReferrers) }} total)</span>
                </h3>
            </div>
            <div class="p-4">
                @if(count($referrers) == 0)
                    <p class="text-gray-500 dark:text-gray-400 text-center italic">{{ __('No data available') }}.</p>
                @else
                    <div class="space-y-4">
                        <div class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <div class="flex-grow pl-2">{{ __('Website') }}</div>
                            <div class="flex items-center">
                                <x-icon name="heroicon-o-users" class="w-4 h-4 mr-1" />
                                {{ __('Visitors') }}
                            </div>
                        </div>

                        @foreach($referrers as $referrer)
                            <div class="group hover:bg-gradient-to-r from-blue-50 to-purple-50 dark:hover:bg-gradient-to-r dark:from-gray-700 dark:to-gray-600 rounded-lg transition-all duration-200 p-2.5 shadow hover:shadow-lg">
                                <div class="flex justify-between items-center mb-1.5">
                                    <div class="flex items-center truncate space-x-2 flex-grow">
                                        @if($referrer->value)
                                            <div class="relative">
                                                <img src="https://icons.duckduckgo.com/ip3/{{ $referrer->value }}.ico" rel="noreferrer" class="w-5 h-5 rounded-full shadow-sm" onerror="this.src='{{ asset('vendor/analytics/icons/globe.svg') }}'">
                                                <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                            </div>
                                            <div class="truncate text-gray-900 dark:text-gray-100 font-medium" dir="ltr">{{ Str::limit($referrer->value, 40) }}</div>
                                            <a href="http://{{ $referrer->value }}" target="_blank" rel="nofollow noreferrer noopener" class="text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                                <x-icon name="heroicon-o-link" class="w-3.5 h-3.5" />
                                            </a>
                                        @else
                                            <div class="bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-800 dark:to-purple-800 p-1 rounded-full">
                                                <x-icon name="heroicon-o-globe-alt" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-300" />
                                            </div>
                                            <div class="truncate text-gray-900 dark:text-gray-100 font-medium">
                                                {{ __('Direct, Email, SMS') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right text-white text-sm font-bold bg-gradient-to-r from-blue-500 to-purple-500 px-2.5 py-0.5 rounded-full shadow-sm">
                                        {{ number_format($referrer->count, 0, __('.'), __(',')) }}
                                    </div>
                                </div>
                                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600 rounded-full h-2 transition-all duration-500 ease-out relative" style="width: {{ $totalReferrers > 0 ? ($referrer->count / $totalReferrers) * 100 : 0 }}%">
                                        <div class="absolute inset-0 bg-white opacity-20 animate-pulse"></div>
                                    </div>
                                </div>
                                <div class="mt-0.5 text-xs text-right text-gray-500 dark:text-gray-400 font-medium">
                                    {{ $totalReferrers > 0 ? number_format(($referrer->count / $totalReferrers) * 100, 1) : 0 }}% of traffic
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            @if(count($referrers) > 0)
                <div class="p-2 bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600">
                    <a href="{{ route('websites.analytics.referrers', ['website' => $website, 'from' => $range['from'], 'to' => $range['to']]) }}" class="flex items-center justify-center text-white hover:text-yellow-200 font-bold text-lg transition-colors group">
                        <span class="mr-3 uppercase tracking-wider">{{ __('View all :count Referrers', ['count' => $totalReferrers]) }}</span>
                        <svg class="w-6 h-6 fill-current transform group-hover:translate-x-1 transition-transform duration-200" viewBox="0 0 24 24">
                            <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </div>