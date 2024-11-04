<div>
    <x-analytics::title
        :title="__('Countries')"
        :totalPageviews="$total"
        :icon="'heroicon-o-map-pin'"
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
    <div>
        @if(count($data) == 0)
            <x-analytics::no-results />
        @else
            <div class="space-y-4">
                <div class="flex items-center justify-between text-lg font-bold text-gray-800 dark:text-gray-100 mt-4 bg-gradient-to-r from-blue-100 to-purple-100 dark:from-blue-900 dark:to-purple-900 p-4 rounded-lg shadow-md">
                    <div class="flex-grow transition-colors duration-300 ease-in-out hover:text-blue-600 dark:hover:text-blue-400">
                        <span class="inline-flex items-center text-2xl">
                            <x-icon name="heroicon-o-map-pin" class="w-6 h-6 mr-3 text-blue-500" />
                            {{ __('Country') }}
                        </span>
                    </div>
                    <div class="transition-colors duration-300 ease-in-out hover:text-blue-600 dark:hover:text-blue-400">
                        <span class="inline-flex items-center text-2xl">
                            <x-icon name="heroicon-o-eye" class="w-6 h-6 mr-3 text-purple-500" />
                            {{ __('Pageviews') }}
                        </span>
                    </div>
                </div>

                @foreach($data as $country)
                    <div class="group hover:bg-gradient-to-r from-blue-50 to-purple-50 dark:hover:bg-gradient-to-r dark:from-gray-700 dark:to-gray-600 rounded-lg transition duration-300 ease-in-out p-3 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                        <div class="flex justify-between items-center mb-2">
                            <div class="flex items-center truncate space-x-2 flex-grow">
                                <div class="bg-blue-100 dark:bg-blue-800 p-1 rounded-full animate-pulse">
                                    <img src="{{ asset('/vendor/analytics/icons/flags/'.formatFlag($country->value)) }}.svg" class="w-4 h-4 text-blue-500 dark:text-blue-300">
                                </div>
                                <div class="truncate text-gray-900 dark:text-gray-100 font-medium text-base" dir="ltr">
                                    @if(!empty(explode(':', $country->value)[1]))
                                        {{ explode(':', $country->value)[1] }}
                                    @else
                                        {{ __('Unknown') }}
                                    @endif
                                </div>
                            </div>
                            <div class="text-right text-white font-bold bg-gradient-to-r from-blue-400 to-purple-500 px-3 py-1 rounded-full shadow-inner hover:shadow-md transition-all duration-300">
                                <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />
                                {{ number_format($country->count, 0, __('.'), __(',')) }}
                            </div>
                        </div>
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full h-3 transition-all duration-500 ease-in-out relative" style="width: {{ ($country->count / $total) * 100 }}%">
                                <div class="absolute inset-0 bg-white opacity-25 animate-pulse"></div>
                            </div>
                        </div>
                        <div class="mt-1 text-xs text-right text-gray-500 dark:text-gray-400">
                            {{ number_format(($country->count / $total) * 100, 2) }}% of total pageviews
                        </div>
                    </div>
                @endforeach
                <x-analytics::pagination :data="$data" />
            </div>
        @endif
    </div>
</div>
