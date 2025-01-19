<x-website :website="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('websites.analytics.overview', ['website' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('websites.analytics.events', ['website' => $site->id]),
                'label' => __('Events'),
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Events')"
            :description="__('Track custom events on your website.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-bars-3-center-left'"
            :totalText="__('Total Events')"
            :data="$data"
            :total="$total"
            :first="$first"
            :last="$last"
            :website="$site"
            :daterange="$daterange"
            :perPage="$perPage"
            :sortBy="$sortBy"
            :sort="$sort"
            :from="$from"
            :sortWords="['count' => __('Events'), 'value' => __('Event Name')]"
            :to="$to"
            :search="$search"
        />

        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: 'compact' }" class="space-y-4">
                    <div class="flex justify-end space-x-2">
                        <button @click="view = 'graph'" :class="{ 'bg-indigo-700': view === 'graph' }" class="p-2 rounded-lg hover:bg-indigo-800 transition-colors">
                            <x-tooltip text="Graph View">
                                <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 text-indigo-100" />
                            </x-tooltip>
                        </button>
                        <button @click="view = 'grid'" :class="{ 'bg-indigo-700': view === 'grid' }" class="p-2 rounded-lg hover:bg-indigo-800 transition-colors">
                            <x-tooltip text="Grid View">
                                <x-icon name="heroicon-o-squares-2x2" class="w-5 h-5 text-indigo-100" />
                            </x-tooltip>
                        </button>
                        <button @click="view = 'list'" :class="{ 'bg-indigo-700': view === 'list' }" class="p-2 rounded-lg hover:bg-indigo-800 transition-colors">
                            <x-tooltip text="List View">
                                <x-icon name="heroicon-o-list-bullet" class="w-5 h-5 text-indigo-100" />
                            </x-tooltip>
                        </button>
                    </div>

                    <div x-show="view === 'list'" class="space-y-4">
                        @foreach($data as $event)
                            <div class="group hover:bg-gradient-to-r from-blue-50 to-purple-50 dark:hover:bg-gradient-to-r dark:from-gray-700 dark:to-gray-600 rounded-lg transition duration-300 ease-in-out p-3 shadow-md hover:shadow-lg transform hover:-translate-y-1">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="flex items-center truncate space-x-2 flex-grow">
                                        <x-icon name="heroicon-o-bars-3-center-left" class="w-4 h-4 mr-2 text-blue-500" />
                                        <div class="truncate text-gray-900 dark:text-gray-100 font-medium text-base" dir="ltr">
                                            {{ explode(':', $event->value)[0] ?: __('Unknown') }}
                                        </div>
                                    </div>
                                    <div class="text-right text-white font-bold bg-gradient-to-r from-blue-400 to-purple-500 px-3 py-1 rounded-full shadow-inner hover:shadow-md transition-all duration-300">
                                        <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 inline mr-1" />
                                        {{ number_format($event->count, 0, __('.'), __(',')) }}
                                    </div>
                                </div>
                                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-full h-3 transition-all duration-500 ease-in-out relative" style="width: {{ ($event->count / $total) * 100 }}%">
                                        <div class="absolute inset-0 bg-white opacity-25 animate-pulse"></div>
                                    </div>
                                </div>
                                <div class="mt-1 text-xs text-right text-gray-500 dark:text-gray-400">
                                    {{ number_format(($event->count / $total) * 100, 2) }}% of total events
                                </div>
                                @if(!empty(explode(':', $event->value)[1]) || !empty(explode(':', $event->value)[2]))
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        @if(!empty(explode(':', $event->value)[1]))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                                {{ number_format((explode(':', $event->value)[1] * $event->count), 2, __('.'), __(',')) }}
                                            </span>
                                        @endif
                                        @if(!empty(explode(':', $event->value)[2]))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ explode(':', $event->value)[2] }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div x-show="view === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($data as $event)
                            <div class="group hover:bg-gradient-to-r from-blue-50 to-purple-50 dark:hover:bg-gradient-to-r dark:from-gray-700 dark:to-gray-600 rounded-lg transition duration-300 ease-in-out p-3 shadow-md hover:shadow-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <div class="flex items-center truncate space-x-2 flex-grow">
                                        <x-icon name="heroicon-o-bars-3-center-left" class="w-4 h-4 mr-2 text-blue-500" />
                                        <div class="truncate text-gray-900 dark:text-gray-100 font-medium text-base" dir="ltr">
                                            {{ explode(':', $event->value)[0] ?: __('Unknown') }}
                                        </div>
                                    </div>
                                    <div class="text-right text-white font-bold bg-gradient-to-r from-blue-400 to-purple-500 px-3 py-1 rounded-full shadow-inner hover:shadow-md transition-all duration-300">
                                        {{ number_format($event->count, 0, __('.'), __(',')) }}
                                    </div>
                                </div>
                                <div class="mt-1 text-xs text-right text-gray-500 dark:text-gray-400">
                                    {{ number_format(($event->count / $total) * 100, 2) }}% of total events
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div x-show="view === 'graph'" class="space-y-4">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <canvas x-data="{
                                init() {
                                    new Chart(this.$el, {
                                        type: 'bar',
                                        data: {
                                            labels: {{ Js::from($data->pluck('value')->map(fn($value) => explode(':', $value)[0] ?: __('Unknown'))) }},
                                            datasets: [{
                                                label: '{{ __('Events') }}',
                                                data: {{ Js::from($data->pluck('count')) }},
                                                backgroundColor: 'rgba(99, 102, 241, 0.5)',
                                                borderColor: 'rgb(99, 102, 241)',
                                                borderWidth: 1
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            scales: {
                                                y: {
                                                    beginAtZero: true
                                                }
                                            }
                                        }
                                    })
                                }
                            }" class="w-full"></canvas>
                        </div>
                    </div>
                </div>
                <x-analytics::pagination :data="$data" />
            @endif
        </div>
    </div>
</x-website>
