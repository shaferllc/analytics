<x-website :website="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('websites.analytics.overview', ['website' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('websites.analytics.geographic', ['website' => $site->id]),
                'label' => __('Geographic'),
            ]
        ]" />

        @include('analytics::livewire.partials.nav')

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('websites.analytics.continents', ['website' => $site->id]) }}" class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-indigo-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-indigo-700 to-indigo-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-globe-americas" class="w-6 h-6 text-indigo-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-indigo-100">{{ number_format($continentsTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-indigo-100">{{ __('Continents') }}</h3>
                <p class="text-indigo-300 text-sm">{{ __('View continent statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.countries', ['website' => $site->id]) }}" class="bg-gradient-to-br from-emerald-900 to-emerald-950 rounded-xl shadow-lg border border-emerald-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-emerald-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-emerald-700 to-emerald-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-flag" class="w-6 h-6 text-emerald-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-emerald-100">{{ number_format($countriesTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-emerald-100">{{ __('Countries') }}</h3>
                <p class="text-emerald-300 text-sm">{{ __('View country statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.cities', ['website' => $site->id]) }}" class="bg-gradient-to-br from-purple-900 to-purple-950 rounded-xl shadow-lg border border-purple-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-purple-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-purple-700 to-purple-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-building-office-2" class="w-6 h-6 text-purple-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-purple-100">{{ number_format($citiesTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-purple-100">{{ __('Cities') }}</h3>
                <p class="text-purple-300 text-sm">{{ __('View city statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.languages', ['website' => $site->id]) }}" class="bg-gradient-to-br from-rose-900 to-rose-950 rounded-xl shadow-lg border border-rose-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-rose-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-rose-700 to-rose-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-language" class="w-6 h-6 text-rose-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-rose-100">{{ number_format($languagesTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-rose-100">{{ __('Languages') }}</h3>
                <p class="text-rose-300 text-sm">{{ __('View language statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.timezones', ['website' => $site->id]) }}" class="bg-gradient-to-br from-yellow-900 to-yellow-950 rounded-xl shadow-lg border border-yellow-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-yellow-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-yellow-700 to-yellow-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-clock" class="w-6 h-6 text-yellow-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-yellow-100">{{ number_format($timezonesTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-yellow-100">{{ __('Timezones') }}</h3>
                <p class="text-yellow-300 text-sm">{{ __('View timezone statistics') }}</p>
            </a>
        </div>
    </div>
</x-website>
