@php
    $pageData = $this->getPageData()['data']->first();
@endphp

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
                'icon' => 'heroicon-o-arrow-path-rounded-square'
            ],
            [
                'url' => route('websites.analytics.pages', ['website' => $site->id]),
                'label' => __('Pages'),
                'icon' => 'heroicon-o-document-text'
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Page')"
            :description="__('Page information for ' . $page)"
            :icon="'heroicon-o-document-text'"
            :website="$site"
            :daterange="$daterange"
            :perPage="$perPage"
            :sortBy="$sortBy"
            :sort="$sort"
            :from="$from"
            :color="'indigo'"
            :sortWords="['count' => __('Pageviews'), 'value' => __('Page')]"
            :to="$to"
            :search="$search"
        >
            @if($page)
                <div x-data="{ showModal: false }" class="rounded-md bg-gray-900/90 p-6 border border-gray-800">
                    <div class="ml-4 space-y-4">
                        <div class="space-y-3">
                            @foreach($pageData->url as $url)
                                <a
                                    href="{{ $site->domain . $url['value'] }}"
                                    target="_blank"
                                    class="flex items-center justify-between bg-indigo-900/50 px-6 py-3 rounded-xl text-indigo-100 hover:text-white hover:bg-indigo-800 transition-all border border-indigo-800 hover:border-indigo-700 shadow-sm hover:shadow-md"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-indigo-800/50 rounded-lg">
                                            <x-icon name="heroicon-o-link" class="w-5 h-5" />
                                        </div>
                                        <span class="text-lg">{{ $url['value'] }}</span>
                                    </div>
                                    <div class="flex items-center text-indigo-300 hover:text-indigo-200">
                                        <span class="mr-2">{{ __('Visit') }}</span>
                                        <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <!-- Title Section -->
                        <div class="space-y-2">
                            @foreach($pageData->title as $value)
                                <h2 class="text-xl font-bold text-gray-100 flex items-center gap-2">
                                    <span class="text-indigo-400 text-base font-normal">
                                        {{ number_format($value['count']) }}×
                                    </span>
                                    <x-tooltip text="{{ __('Number of times this title was seen') }}">
                                        {{ $value['value'] }}
                                    </x-tooltip>
                                </h2>
                            @endforeach

                            @if(count($pageData->title) > 1)
                                <div class="flex items-center gap-2 text-sm text-yellow-500 bg-yellow-500/10 px-3 py-2 rounded-lg">
                                    <x-icon name="heroicon-o-exclamation-triangle" class="w-5 h-5" />
                                    <p>{{ __('This page has multiple titles, suggesting dynamic content or renaming.') }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Stats & Actions -->
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center bg-indigo-500/10 px-3 py-1.5 rounded-lg text-indigo-400">
                                <x-icon name="heroicon-o-eye" class="w-4 h-4 mr-2" />
                                <span class="font-medium">{{ number_format($pageData->count) }}</span>
                                <span class="ml-1 text-indigo-300">{{ __('views') }}</span>
                            </div>

                            <button
                                @click="showModal = true"
                                class="flex items-center bg-indigo-500/10 px-3 py-1.5 rounded-lg text-indigo-400 hover:bg-indigo-500/20 transition-colors"
                            >
                                <x-icon name="heroicon-o-information-circle" class="w-4 h-4 mr-2" />
                                {{ __('View Details') }}
                            </button>


                        </div>
                    </div>

                    <!-- Modal -->
                    @include('analytics::livewire.partials.page.modal')
                </div>
            @endif
        </x-analytics::title>

            <div x-data="{ activeTab: 'os' }" class="mb-8">
                <div class="border-b border-indigo-800 mb-4">
                    <nav class="flex -mb-px space-x-8">
                        @php
                        $tabs = [
                            'technical' => [
                                'label' => __('Technical'),
                                'icon' => 'heroicon-o-cpu-chip',
                                'items' => [
                                    'os' => [
                                        'icon' => 'heroicon-o-cpu-chip',
                                        'label' => __('OS')
                                    ],
                                    'browsers' => [
                                        'icon' => 'heroicon-o-globe-alt',
                                        'label' => __('Browsers')
                                    ],
                                    'viewport' => [
                                        'icon' => 'heroicon-o-square-2-stack',
                                        'label' => __('Viewport')
                                    ],
                                    'jsError' => [
                                        'icon' => 'heroicon-o-exclamation-circle',
                                        'label' => __('JS Errors')
                                    ]
                                ]
                            ],
                            'analytics' => [
                                'label' => __('Analytics'),
                                'icon' => 'heroicon-o-chart-bar',
                                'items' => [
                                    'locations' => [
                                        'icon' => 'heroicon-o-map',
                                        'label' => __('Locations')
                                    ],
                                    'referrers' => [
                                        'icon' => 'heroicon-o-arrow-uturn-left',
                                        'label' => __('Referrers')
                                    ],
                                    'searchEngine' => [
                                        'icon' => 'heroicon-o-magnifying-glass',
                                        'label' => __('Search Engines')
                                    ],
                                    'sessions' => [
                                        'icon' => 'heroicon-o-cursor-arrow-rays',
                                        'label' => __('Sessions')
                                    ],
                                    'outbound_links' => [
                                        'icon' => 'heroicon-o-arrow-right',
                                        'label' => __('Outbound Links')
                                    ]
                                ]
                            ],
                            'reports' => [
                                'label' => __('Reports'),
                                'icon' => 'heroicon-o-document-text',
                                'items' => [
                                    'reports' => [
                                        'icon' => 'heroicon-o-document-text',
                                        'label' => __('Reports')
                                    ]
                                ]
                            ]
                        ];
                        @endphp

                        @foreach($tabs as $groupId => $group)
                            <div x-data="{ open: false }" class="relative">
                                <button
                                    @click="open = !open"
                                    :class="{ 'border-indigo-500 text-indigo-300': activeTab.startsWith('{{ $groupId }}') || open, 'border-transparent text-indigo-500 hover:text-indigo-400 hover:border-indigo-400': !activeTab.includes('{{ $groupId }}') && !open }"
                                    class="px-1 py-4 text-sm font-medium border-b-2 transition-colors duration-200">
                                    <div class="flex items-center space-x-2">
                                        <x-icon name="{{ $group['icon'] }}" class="w-5 h-5" />
                                        <span>{{ $group['label'] }}</span>
                                        <x-icon name="heroicon-o-chevron-down" class="w-4 h-4" />
                                    </div>
                                </button>

                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    x-transition
                                    class="absolute z-10 mt-2 bg-indigo-900 border border-indigo-700 rounded-lg shadow-lg w-48">
                                    @foreach($group['items'] as $id => $tab)
                                        <button
                                            @click="activeTab = '{{ $id }}'; open = false"
                                            :class="{ 'bg-indigo-800': activeTab === '{{ $id }}' }"
                                            class="w-full px-4 py-2 text-left text-sm text-indigo-200 hover:bg-indigo-800 flex items-center space-x-2">
                                            <x-icon name="{{ $tab['icon'] }}" class="w-5 h-5" />
                                            <span>{{ $tab['label'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </nav>
                </div>

                @include('analytics::livewire.partials.page.os')
                @include('analytics::livewire.partials.page.browsers')
                @include('analytics::livewire.partials.page.viewport')
                include('analytics::livewire.partials.page.locations')
                include('analytics::livewire.partials.page.search-engine')
                include('analytics::livewire.partials.page.js')
                include('analytics::livewire.partials.page.sessions')
                include('analytics::livewire.partials.page.outbound-links')
                include('analytics::livewire.partials.page.reports')
            </div>
        </div>
    </div>
</x-website>
