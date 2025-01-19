<x-website :website="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('websites.analytics.overview', ['website' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('websites.analytics.acquisitions', ['website' => $site->id]),
                'label' => __('Acquisitions'),
            ],
            [
                'url' => route('websites.analytics.referrers', ['website' => $site->id]),
                'label' => __('Referrers'),
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Referrers')"
            :description="__('Track where your visitors are coming from.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-link'"
            :totalText="__('Total Referrers')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('Referrer')]"
            :to="$to"
            :search="$search"
        />

        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: '{{ $display }}' }" class="space-y-4">
                    <x-analytics::view-switcher :data="$data" color="indigo" />

                    <x-analytics::view view="list" color="indigo" class="space-y-4">
                        @foreach($data as $referrer)
                            <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-indigo-800/20 blur-xl rounded-full"></div>
                                            <div class="relative bg-gradient-to-br from-indigo-700 to-indigo-900 p-3 rounded-full">
                                                <x-tooltip :text="__('Referrer Source')">
                                                    <x-icon name="heroicon-o-link" class="w-6 h-6 text-indigo-200" />
                                                </x-tooltip>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-indigo-100 max-w-full">
                                                {{ $referrer->value ?: __('Direct, Email, SMS') }}
                                            </h3>
                                            <x-tooltip :text="__('Percentage of total traffic')">
                                                <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-700/50 text-indigo-200">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 mr-1" />
                                                    {{ number_format(($referrer->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of total') }}
                                                </div>
                                            </x-tooltip>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <x-tooltip :text="__(':count pageviews from :referrer', ['count' => number_format($referrer->count, 0, __('.'), __(',')), 'referrer' => $referrer->value])">
                                            <span class="text-2xl font-bold text-indigo-100">
                                                {{ number_format($referrer->count, 0, __('.'), __(',')) }}
                                            </span>
                                        </x-tooltip>
                                        <p class="text-indigo-300 text-sm">{{ __('Pageviews') }}</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="overflow-hidden h-2 text-xs flex rounded-full bg-indigo-700/30">
                                        <div style="width: {{ ($referrer->count / $aggregates['total_count']) * 100 }}%"
                                            class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-400 to-indigo-600">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-analytics::view>

                    <x-analytics::view view="compact" color="indigo" class="overflow-hidden rounded-xl border border-indigo-800">
                        <table class="min-w-full divide-y divide-indigo-800">
                            <thead class="bg-indigo-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-indigo-300 uppercase tracking-wider">
                                        <x-icon name="heroicon-o-link" class="w-4 h-4 inline mr-1" />
                                        {{ __('Referrer') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">
                                        <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />
                                        {{ __('Pageviews') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">
                                        <x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1" />
                                        {{ __('Percentage') }}
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-indigo-300 uppercase tracking-wider">
                                        <x-icon name="heroicon-o-chart-bar" class="w-4 h-4 inline mr-1" />
                                        {{ __('Graph') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-indigo-950 divide-y divide-indigo-800">
                                @foreach($data as $referrer)
                                    <tr class="hover:bg-indigo-900/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-indigo-100 max-w-xs" x-data="{ showFull: false }" @mouseenter="showFull = true" @mouseleave="showFull = false">
                                                    <x-tooltip :text="$referrer->value">
                                                        <span x-show="showFull" class="relative inline-block bg-indigo-900 rounded-lg p-2">{{ $referrer->value ?: __('Direct, Email, SMS') }}</span>
                                                        <span x-show="!showFull" x-cloak class="relative inline-block bg-indigo-900 rounded-lg p-2">{{ Str::limit($referrer->value, 90) ?: __('Direct, Email, SMS') }}</span>
                                                    </x-tooltip>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-indigo-100">
                                            <x-tooltip :text="__('Total pageviews')">
                                                {{ number_format($referrer->count, 0, __('.'), __(',')) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-indigo-100">
                                            <x-tooltip :text="__('Percentage of total traffic')">
                                                {{ number_format(($referrer->count / $aggregates['total_count']) * 100, 1) }}%
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-32 h-2 text-xs flex rounded-full ml-auto">
                                                <div style="width: {{ ($referrer->count / $aggregates['total_count']) * 100 }}%"
                                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-400 to-indigo-600">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>

                    <x-analytics::view view="cards" color="indigo" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($data as $referrer)
                            <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6 backdrop-blur-xl hover:scale-[1.02] transition-transform duration-300">
                                <div class="flex flex-col space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm font-medium text-indigo-100 break-all">
                                            {{ $referrer->value ?: __('Direct, Email, SMS') }}
                                        </div>
                                        <x-tooltip :text="__('Total pageviews from this referrer')">
                                            <div class="text-xl font-bold text-indigo-100">
                                                {{ number_format($referrer->count, 0, __('.'), __(',')) }}
                                            </div>
                                        </x-tooltip>
                                    </div>

                                    <div class="space-y-3">
                                        <x-tooltip :text="__('Visual representation of pageview share')">
                                            <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-indigo-700/30">
                                                <div style="width: {{ ($referrer->count / $aggregates['total_count']) * 100 }}%"
                                                    class="shadow-lg bg-gradient-to-r from-indigo-400 to-indigo-600">
                                                </div>
                                            </div>
                                        </x-tooltip>

                                        <div class="flex justify-between text-xs text-indigo-300">
                                            <div class="flex space-x-4">
                                                <x-tooltip :text="__('First seen')">
                                                    <span class="hover:text-indigo-200 transition-colors">
                                                        <x-icon name="heroicon-o-clock" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                        {{ $referrer->created_at->diffForHumans() }}
                                                    </span>
                                                </x-tooltip>
                                                <x-tooltip :text="__('Last seen')">
                                                    <span class="hover:text-indigo-200 transition-colors">
                                                        <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                        {{ $referrer->updated_at->diffForHumans() }}
                                                    </span>
                                                </x-tooltip>
                                            </div>
                                            <x-tooltip :text="__('Percentage of total pageviews')">
                                                <span class="hover:text-indigo-200 transition-colors">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1 text-indigo-300" />
                                                    {{ number_format(($referrer->count / $aggregates['total_count']) * 100, 1) }}%
                                                </span>
                                            </x-tooltip>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-analytics::view>
                </div>

                <div class="mt-6">
                    <x-analytics::pagination :data="$data" />
                </div>
            @endif
        </div>
    </div>
</x-website>
