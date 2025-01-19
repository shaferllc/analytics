<x-website :website="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('websites.analytics.overview', ['website' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('websites.analytics.technology', ['website' => $site->id]),
                'label' => __('Technology'),
            ]
        ]" />

        @include('analytics::livewire.partials.nav')

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="{{ route('websites.analytics.operating-systems', ['website' => $site->id]) }}" class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-indigo-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-indigo-700 to-indigo-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-computer-desktop" class="w-6 h-6 text-indigo-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-indigo-100">{{ number_format($operatingSystemsTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-indigo-100">{{ __('Operating Systems') }}</h3>
                <p class="text-indigo-300 text-sm">{{ __('View operating system statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.browsers', ['website' => $site->id]) }}" class="bg-gradient-to-br from-emerald-900 to-emerald-950 rounded-xl shadow-lg border border-emerald-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-emerald-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-emerald-700 to-emerald-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-globe-alt" class="w-6 h-6 text-emerald-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-emerald-100">{{ number_format($browsersTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-emerald-100">{{ __('Browsers') }}</h3>
                <p class="text-emerald-300 text-sm">{{ __('View browser usage statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.screen-resolutions', ['website' => $site->id]) }}" class="bg-gradient-to-br from-purple-900 to-purple-950 rounded-xl shadow-lg border border-purple-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-purple-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-purple-700 to-purple-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-rectangle-stack" class="w-6 h-6 text-purple-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-purple-100">{{ number_format($screenResolutionsTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-purple-100">{{ __('Screen Resolutions') }}</h3>
                <p class="text-purple-300 text-sm">{{ __('View screen resolution statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.devices', ['website' => $site->id]) }}" class="bg-gradient-to-br from-rose-900 to-rose-950 rounded-xl shadow-lg border border-rose-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-rose-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-rose-700 to-rose-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-device-phone-mobile" class="w-6 h-6 text-rose-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-rose-100">{{ number_format($devicesTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-rose-100">{{ __('Devices') }}</h3>
                <p class="text-rose-300 text-sm">{{ __('View device type statistics') }}</p>
            </a>

            <a href="{{ route('websites.analytics.user-agents', ['website' => $site->id]) }}" class="bg-gradient-to-br from-orange-900 to-orange-950 rounded-xl shadow-lg border border-orange-800 p-6 hover:scale-[1.02] transition-transform duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            <div class="absolute inset-0 bg-orange-800/20 blur-xl rounded-full"></div>
                            <div class="relative bg-gradient-to-br from-orange-700 to-orange-900 p-3 rounded-full">
                                <x-icon name="heroicon-o-user" class="w-6 h-6 text-orange-200" />
                            </div>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-orange-100">{{ number_format($userAgentsTotal ?? 0) }}</div>
                </div>
                <h3 class="text-lg font-semibold text-orange-100">{{ __('User Agents') }}</h3>
                <p class="text-orange-300 text-sm">{{ __('View user agent statistics') }}</p>
            </a>
        </div>
    </div>
</x-website>
