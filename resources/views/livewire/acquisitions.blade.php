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
            ]
        ]" />

        @include('analytics::livewire.partials.nav')

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('websites.analytics.referrers', ['website' => $site->id]) }}" class="bg-gradient-to-br from-blue-900 to-blue-950 rounded-xl shadow-lg border border-blue-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-blue-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-blue-700 to-blue-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-computer-desktop" class="w-6 h-6 text-blue-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-blue-100">{{ number_format($referrers ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-blue-100">{{ __('Referrers') }}</h3>
                <p class="text-blue-300 text-sm">{{ __('View referrer statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.search-engines', ['website' => $site->id]) }}" class="bg-gradient-to-br from-emerald-900 to-emerald-950 rounded-xl shadow-lg border border-emerald-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-emerald-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-emerald-700 to-emerald-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-magnifying-glass" class="w-6 h-6 text-emerald-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-emerald-100">{{ number_format($searchEngines ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-emerald-100">{{ __('Search Engines') }}</h3>
                <p class="text-emerald-300 text-sm">{{ __('View search engine statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.social-networks', ['website' => $site->id]) }}" class="bg-gradient-to-br from-purple-900 to-purple-950 rounded-xl shadow-lg border border-purple-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-purple-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-purple-700 to-purple-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-share" class="w-6 h-6 text-purple-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-purple-100">{{ number_format($socialNetworks ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-purple-100">{{ __('Social Networks') }}</h3>
                <p class="text-purple-300 text-sm">{{ __('View social network statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.campaigns', ['website' => $site->id]) }}" class="bg-gradient-to-br from-amber-900 to-amber-950 rounded-xl shadow-lg border border-amber-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-amber-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-amber-700 to-amber-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-flag" class="w-6 h-6 text-amber-200" />
                            </div>
                        </div>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-amber-100">{{ __('Campaigns') }}</h3>
                <p class="text-amber-300 text-sm">{{ __('View campaign statistics') }}</p>
            </a>
        </div>

    </div>
</x-website>
