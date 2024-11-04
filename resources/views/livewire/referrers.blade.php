<div>
    <x-analytics::title
        :title="__('Referrers')"
        :totalPageviews="$total"
        :icon="'heroicon-o-link'"
        :totalText="__('Total Visitors')"
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
    <div>
        @if(count($data) == 0)
            <x-analytics::no-results />
        @else
            <div class="space-y-4">
                <div class="flex items-center justify-between text-lg font-bold text-gray-800 dark:text-gray-100 mt-4 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900 dark:to-purple-900 p-4 rounded-lg shadow-md">
                    <div class="flex-grow transition-colors duration-300 ease-in-out hover:text-blue-600 dark:hover:text-blue-400">
                        <span class="inline-flex items-center text-2xl">
                            <x-icon name="heroicon-o-link" class="w-6 h-6 mr-3 text-blue-500" />
                            {{ __('Referrer') }}
                        </span>
                    </div>
                    <div class="transition-colors duration-300 ease-in-out hover:text-blue-600 dark:hover:text-blue-400">
                        <span class="inline-flex items-center text-2xl">
                            <x-icon name="heroicon-o-users" class="w-6 h-6 mr-3 text-purple-500" />
                            {{ __('Visitors') }}
                        </span>
                    </div>
                </div>

                @foreach($data as $referrer)
                    <div class="group hover:bg-gradient-to-r from-blue-50 to-purple-50 dark:hover:bg-gradient-to-r dark:from-gray-700 dark:to-gray-600 rounded-lg transition duration-300 ease-in-out p-3 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center truncate space-x-2 flex-grow">
                                @if($referrer->value)
                                    <img src="https://icons.duckduckgo.com/ip3/{{ $referrer->value }}.ico" rel="noreferrer" class="w-4 h-4 mr-2">
                                    <div class="truncate text-gray-900 dark:text-gray-100 font-medium text-base" dir="ltr">
                                        {{ $referrer->value }}
                                    </div>
                                    <a href="http://{{ $referrer->value }}" target="_blank" rel="nofollow noreferrer noopener" class="text-gray-500 flex items-center">
                                        <x-icon name="heroicon-o-link" class="w-4 h-4" />
                                    </a>
                                @else
                                    <img src="{{ asset('/images/icons/referrers/unknown.svg') }}" rel="noreferrer" class="w-4 h-4 mr-2">
                                    <div class="truncate text-gray-900 dark:text-gray-100 font-medium text-base">
                                        {{ __('Direct, Email, SMS') }}
                                    </div>
                                @endif
                            </div>
                            <div class="text-right text-white font-bold bg-gradient-to-r from-blue-400 to-purple-500 px-3 py-1 rounded-full shadow-inner hover:shadow-md transition-all duration-300">
                                <x-icon name="heroicon-o-users" class="w-4 h-4 inline mr-1" />
                                {{ number_format($referrer->count, 0, __('.'), __(',')) }}
                            </div>
                        </div>
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full h-3 transition-all duration-500 ease-in-out relative" style="width: {{ ($referrer->count / $total) * 100 }}%">
                                <div class="absolute inset-0 bg-white opacity-25 animate-pulse"></div>
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-right text-gray-500 dark:text-gray-400">
                            {{ number_format(($referrer->count / $total) * 100, 2) }}% of total visitors
                        </div>
                    </div>
                @endforeach
            </div>
            <x-analytics::pagination :data="$data" />
        @endif
    </div>
</div>