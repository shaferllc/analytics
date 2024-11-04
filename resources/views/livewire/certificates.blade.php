<div>
    
    include('analytics::livewire.domain.partials.header')
    <!-- Main Tabs -->
    <div x-data="{ activeTab: 'certificates' }" class="mb-8">
        include('analytics::livewire.domain.partials.tabs')

        <!-- Search & Filters -->
        

        <!-- Certificates Tab Content -->
        <div x-show="activeTab === 'certificates'" x-cloak class="mt-6" wire:loading.class="opacity-50">

            <!-- Certificates Overview Header -->
            <div class="mb-10 bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-8 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-pattern opacity-10"></div>
                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-white/20 rounded-xl">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-bold">SSL Certificate Management</h2>
                            <p class="mt-2 text-emerald-100">Monitor and manage SSL certificates across your domains</p>
                            <div class="flex items-center mt-4 space-x-3">
                                <span class="px-3 py-1 text-sm bg-white/20 rounded-full">{{ $website->domain }}</span>
                                <span class="px-3 py-1 text-sm bg-white/20 rounded-full">{{ count($certificates ?? []) }} Certificates</span>
                                <span class="px-3 py-1 text-sm bg-white/20 rounded-full">Last Scan: {{ now()->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col items-end space-y-3">
                        <div class="flex items-center space-x-2">
                            @if($securityInsights['ssl']['valid'] ?? false)
                                <span class="px-4 py-2 bg-emerald-500/30 text-emerald-100 rounded-xl font-medium">SSL Valid</span>
                            @else
                                <span class="px-4 py-2 bg-red-500/30 text-red-100 rounded-xl font-medium">SSL Invalid</span>
                            @endif
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-sm text-emerald-100">Certificate Status</span>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="px-3 py-1 text-xs bg-white/20 rounded-full">{{ $certificates ? $certificates->filter(fn($cert) => $cert->was_valid)->count() : 0 }} Active</span>
                                <span class="px-3 py-1 text-xs bg-white/20 rounded-full">{{ $certificates ? $certificates->filter(fn($cert) => !$cert->was_valid)->count() : 0 }} Expired</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-8">
                @include('analytics::livewire.partials.certificates.filters')
            </div>

            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Sidebar -->
                <div class="lg:w-1/4">
                    @include('analytics::livewire.partials.certificates.overview')
                    @include('analytics::livewire.partials.certificates.coverage')
                    @include('analytics::livewire.partials.certificates.distribution')
                    @include('analytics::livewire.partials.certificates.analytics')
                </div>

                <!-- Main Content Area -->
                <div class="lg:w-3/4">
                    <!-- Certificates List -->
                    <div class="space-y-6">
                        @forelse($certificates as $certificate)
                            <div x-data="{ expanded: false }" class="bg-white/80 dark:bg-gray-800/90 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-gray-200/50 dark:border-gray-700/50 hover:shadow-2xl transition-all duration-300">
                                <!-- Certificate Header -->
                                @include('analytics::livewire.partials.certificates.header')

                                @if($certificate)
                                    @if($certificate->was_valid)
                                        <!-- Certificate Info Grid -->
                                        @include('analytics::livewire.partials.certificates.grid')

                                        <!-- Progress Bar -->
                                        @include('analytics::livewire.partials.certificates.progress-bar')

                                        <!-- Expand Button -->
                                        <button @click="expanded = !expanded" class="w-full mt-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center">
                                            <span>{{ __('Show More Details') }}</span>
                                            <template x-if="expanded">
                                                <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 ml-2 transform transition-transform" />
                                            </template>
                                            <template x-if="!expanded">
                                                <x-icon name="heroicon-o-chevron-up" class="w-4 h-4 ml-2 transform transition-transform" />
                                            </template>
                                        </button>

                                        <!-- Expanded Content -->
                                        <div x-show="expanded" x-collapse class="mt-4 space-y-4">
                                            @include('analytics::livewire.partials.certificates.additional-domains')
                                            @include('analytics::livewire.partials.certificates.chain')
                                            @include('analytics::livewire.partials.certificates.timeline')
                                        </div>
                                    @else
                                        <!-- Expired Certificate - Minimal Info -->
                                        @include('analytics::livewire.partials.certificates.expired')
                                    @endif
                                @else
                                    <div class="text-center p-4">
                                        <div class="text-gray-600 dark:text-gray-400">
                                            {{ __('This domain does not have an SSL certificate installed.') }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            @include('analytics::livewire.partials.certificates.no-certificates')
                        @endforelse

                        <div class="mt-4">
                            {{ $certificates->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DNS Tab Content -->
        <div x-show="activeTab === 'dns'" x-cloak class="mt-6">
            include('analytics::livewire.partials.dns.info')
        </div>

        <!-- DNS Propagation Tab Content -->
        <div x-show="activeTab === 'propagation'" x-cloak class="mt-6">
            include('analytics::livewire.partials.propogation.info')
        </div>

        <!-- Registry Tab Content -->
        <div x-show="activeTab === 'registry'" x-cloak class="mt-6">
            include('analytics::livewire.partials.registry.info')
        </div>

        <!-- Robots Tab Content -->
        <div x-show="activeTab === 'robots'" x-cloak class="mt-6">
            include('analytics::livewire.domain.robots')
        </div>

        <!-- Opengraph Tab Content -->
        <div x-show="activeTab === 'opengraph'" x-cloak class="mt-6">
            include('analytics::livewire.domain.opengraph')
        </div>

        <!-- Security Tab Content -->
        <div x-show="activeTab === 'security'" x-cloak class="mt-6">
            include('analytics::livewire.domain.security')
        </div>
    </div>
</div>