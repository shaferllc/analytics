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
                'url' => route('websites.analytics.landing-pages', ['website' => $site->id]),
                'label' => __('Landing Pages'),
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Landing Pages')"
            :description="__('Track which pages visitors land on first.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-document-text'"
            :totalText="__('Total Landing Pages')"
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
            :sortWords="['count' => __('Pageviews'), 'value' => __('Landing Page')]"
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
                        @foreach($data as $landingPage)
                            <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            <h3 class="text-lg font-semibold text-indigo-100 max-w-full">
                                                <x-tooltip :text="$landingPage->value ?: __('Unknown')">
                                                    <x-icon name="heroicon-o-link" class="w-4 h-4 inline mr-1" />
                                                    {{ $landingPage->value ?: __('Unknown') }}
                                                </x-tooltip>
                                            </h3>
                                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-700/50 text-indigo-200">
                                                <x-tooltip :text="__('Percentage of total pageviews')">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                    {{ number_format(($landingPage->count / $total) * 100, 1) }}% {{ __('of total') }}
                                                </x-tooltip>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <x-tooltip :text="__('Total pageviews for this landing page')">
                                            <span class="text-2xl font-bold text-indigo-100">
                                                <x-icon name="heroicon-o-eye" class="w-5 h-5 inline mr-1" />
                                                {{ number_format($landingPage->count, 0, __('.'), __(',')) }}
                                            </span>
                                        </x-tooltip>
                                        <p class="text-indigo-300 text-sm">{{ __('Pageviews') }}</p>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="overflow-hidden h-2 text-xs flex rounded-full bg-indigo-700/30">
                                        <div style="width: {{ ($landingPage->count / $total) * 100 }}%"
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
                                        {{ __('Landing Page') }}
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
                                @foreach($data as $landingPage)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="text-sm font-medium text-indigo-100 max-w-xs" x-data="{ showFull: false }" @mouseenter="showFull = true" @mouseleave="showFull = false">
                                                    <x-tooltip :text="$landingPage->value ?: __('Unknown')">
                                                        <span x-show="showFull" class="relative inline-block bg-indigo-900 rounded-lg p-2">{{ $landingPage->value ?: __('Unknown') }}</span>
                                                        <span x-show="!showFull" x-cloak class="relative inline-block bg-indigo-900 rounded-lg p-2">{{ Str::limit($landingPage->value, 90) ?: __('Unknown') }}</span>
                                                    </x-tooltip>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-indigo-100">
                                            <x-tooltip :text="__('Total pageviews for this landing page')">
                                                {{ number_format($landingPage->count, 0, __('.'), __(',')) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-indigo-100">
                                            <x-tooltip :text="__('Percentage of total pageviews')">
                                                {{ number_format(($landingPage->count / $total) * 100, 1) }}%
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-32 h-2 text-xs flex rounded-full ml-auto">
                                                <div style="width: {{ ($landingPage->count / $total) * 100 }}%"
                                                    class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-indigo-400 to-indigo-600">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>

                    <x-analytics::view view="cards" color="indigo" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($data as $landingPage)
                            <div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-4 hover:scale-[1.02] transition-transform duration-200">
                                <div class="mb-4">
                                    <div class="text-sm text-indigo-100 break-all">
                                        <x-tooltip :text="$landingPage->value ?: __('Unknown')">
                                            <x-icon name="heroicon-o-link" class="w-4 h-4 inline mr-1" />
                                            {{ Str::limit($landingPage->value, 90) ?: __('Unknown') }}
                                        </x-tooltip>
                                    </div>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <x-tooltip :text="__('Total pageviews for this landing page')">
                                            <div class="bg-indigo-800/20 rounded-lg p-3 hover:bg-indigo-700/30 transition-colors duration-200">
                                                <span class="text-indigo-300">
                                                    <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1" />
                                                    {{ __('Pageviews') }}
                                                </span>
                                                <span class="text-indigo-100 font-semibold ml-2">{{ number_format($landingPage->count, 0, __('.'), __(',')) }}</span>
                                            </div>
                                        </x-tooltip>
                                        <x-tooltip :text="__('Percentage of total pageviews')">
                                            <div class="bg-indigo-800/20 rounded-lg p-3 hover:bg-indigo-700/30 transition-colors duration-200">
                                                <span class="text-indigo-300">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                    {{ __('Share') }}
                                                </span>
                                                <span class="text-indigo-100 font-semibold ml-2">{{ number_format(($landingPage->count / $total) * 100, 1) }}%</span>
                                            </div>
                                        </x-tooltip>
                                    </div>
                                    <div class="relative pt-1">
                                        <div class="overflow-hidden h-3 text-xs flex rounded-lg bg-indigo-700/30">
                                            <div style="width: {{ ($landingPage->count / $total) * 100 }}%"
                                                class="shadow-none bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all duration-500 hover:from-indigo-500 hover:to-indigo-700">
                                            </div>
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
