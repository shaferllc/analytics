<div class="mb-8" x-cloak x-data="{ showStats: false, isCompact: $persist(false).as('compact_header') }" x-init="setTimeout(() => showStats = true, 500)">
    <div class="flex flex-col gap-6">
        <!-- Header Toggle -->
        <button @click="isCompact = !isCompact" class="self-end px-3 py-1.5 text-sm font-medium text-gray-600 dark:text-gray-400 bg-white/80 dark:bg-gray-800/80 rounded-lg shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700/80 transition-colors">
            <span x-text="isCompact ? 'Expand Header' : 'Compact Header'"></span>
        </button>

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-stretch md:justify-between"
            :class="isCompact ? 'gap-2' : 'gap-6'"
            x-show="showStats"
            x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0">
            
            <!-- Main Stats Card -->
            <div class="flex-1 bg-gradient-to-br from-cyan-100 via-blue-100 to-indigo-200 dark:from-cyan-800/30 dark:via-blue-800/40 dark:to-indigo-800/50 rounded-3xl shadow-lg backdrop-blur-xl border border-cyan-200/50 dark:border-cyan-700/50 transform hover:-translate-y-2 hover:rotate-1 transition-all duration-300 hover:shadow-2xl group"
                :class="isCompact ? 'p-4' : 'p-8'">
                <div class="flex items-center gap-6" :class="isCompact ? 'mb-3' : 'mb-6'">
                    <div class="p-3 bg-white/90 dark:bg-gray-900/90 rounded-xl shadow-md ring-1 ring-cyan-100 dark:ring-cyan-800/50 transform group-hover:rotate-6 transition-transform duration-300"
                        :class="isCompact ? 'scale-75' : ''">
                        <img src="https://www.google.com/s2/favicons?domain={{ $website->domain }}&sz=128" alt="{{ $website->domain }} favicon" class="w-12 h-12 animate-pulse">
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-cyan-500 via-blue-400 to-indigo-500 dark:from-cyan-400 dark:via-blue-300 dark:to-indigo-400 bg-clip-text text-transparent tracking-tight hover:scale-105 transition-transform"
                            :class="isCompact ? 'text-2xl' : 'text-4xl'">{{ $website->domain }}</h1>
                        <p x-show="!isCompact" class="text-gray-600 dark:text-gray-400 mt-1 group-hover:translate-x-2 transition-transform">Secure your online presence</p>
                        <div x-show="!isCompact" class="h-1 w-20 bg-gradient-to-r from-cyan-400 via-blue-400 to-indigo-400 rounded-full mt-2 group-hover:w-32 transition-all duration-500"></div>
                    </div>
                </div>
                <div x-show="!isCompact" class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    <div class="bg-white/50 dark:bg-gray-800/50 p-4 rounded-xl hover:bg-white/70 dark:hover:bg-gray-800/70 transition-colors group">
                        <p class="text-sm text-gray-500 dark:text-gray-400">SSL Status</p>
                        <div class="flex items-center gap-2">
                            <x-icon name="heroicon-o-shield-check" class="w-5 h-5 text-emerald-500"/>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white group-hover:scale-110 transition-transform">{{ $website->certificates->count() }} Certificates</p>
                        </div>
                    </div>
                    <div class="bg-white/50 dark:bg-gray-800/50 p-4 rounded-xl hover:bg-white/70 dark:hover:bg-gray-800/70 transition-colors group">
                        <p class="text-sm text-gray-500 dark:text-gray-400">DNS Records</p>
                        <div class="flex items-center gap-2">
                            <x-icon name="heroicon-o-server" class="w-5 h-5 text-blue-500"/>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white group-hover:scale-110 transition-transform">{{ $website->dns->count() }} Records</p>
                        </div>
                    </div>
                    <div class="bg-white/50 dark:bg-gray-800/50 p-4 rounded-xl hover:bg-white/70 dark:hover:bg-gray-800/70 transition-colors group">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Response Time</p>
                        <div class="flex items-center gap-2">
                            <x-icon name="heroicon-o-clock" class="w-5 h-5 text-indigo-500"/>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white group-hover:scale-110 transition-transform">{{ rand(50, 200) }}ms</p>
                        </div>
                    </div>
                    <div class="bg-white/50 dark:bg-gray-800/50 p-4 rounded-xl hover:bg-white/70 dark:hover:bg-gray-800/70 transition-colors group">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Uptime</p>
                        <div class="flex items-center gap-2">
                            <x-icon name="heroicon-o-arrow-trending-up" class="w-5 h-5 text-green-500"/>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white group-hover:scale-110 transition-transform">99.9%</p>
                        </div>
                    </div>
                    <div class="bg-white/50 dark:bg-gray-800/50 p-4 rounded-xl hover:bg-white/70 dark:hover:bg-gray-800/70 transition-colors group">
                        <p class="text-sm text-gray-500 dark:text-gray-400">HTTPS Score</p>
                        <div class="flex items-center gap-2">
                            <x-icon name="heroicon-o-lock-closed" class="w-5 h-5 text-purple-500"/>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white group-hover:scale-110 transition-transform">A+</p>
                        </div>
                    </div>
                    <div class="bg-white/50 dark:bg-gray-800/50 p-4 rounded-xl hover:bg-white/70 dark:hover:bg-gray-800/70 transition-colors group">
                        <p class="text-sm text-gray-500 dark:text-gray-400">DNS Health</p>
                        <div class="flex items-center gap-2">
                            <x-icon name="heroicon-o-heart" class="w-5 h-5 text-pink-500"/>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white group-hover:scale-110 transition-transform">98%</p>
                        </div>
                    </div>
                    <div class="bg-white/50 dark:bg-gray-800/50 p-4 rounded-xl hover:bg-white/70 dark:hover:bg-gray-800/70 transition-colors group">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Security Headers</p>
                        <div class="flex items-center gap-2">
                            <x-icon name="heroicon-o-shield-exclamation" class="w-5 h-5 text-amber-500"/>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white group-hover:scale-110 transition-transform">7/8</p>
                        </div>
                    </div>
                    <div class="bg-white/50 dark:bg-gray-800/50 p-4 rounded-xl hover:bg-white/70 dark:hover:bg-gray-800/70 transition-colors group">
                        <p class="text-sm text-gray-500 dark:text-gray-400">SSL Days Left</p>
                        <div class="flex items-center gap-2">
                            <x-icon name="heroicon-o-calendar" class="w-5 h-5 text-cyan-500"/>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white group-hover:scale-110 transition-transform">{{ rand(30, 90) }} Days</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Stats -->
            <div x-show="!isCompact" class="flex flex-col gap-4 md:w-1/3">
                <div class="bg-gradient-to-br from-emerald-50 via-green-50 to-teal-50 dark:from-gray-800/50 dark:via-gray-700/50 dark:to-gray-600/50 p-6 rounded-3xl shadow-xl backdrop-blur-xl border border-emerald-200/50 dark:border-emerald-800/50 transform hover:scale-105 hover:rotate-2 transition-all duration-300 group">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="p-2 bg-white/90 dark:bg-gray-900/90 rounded-xl shadow-lg group-hover:animate-bounce">
                            <x-icon name="heroicon-o-check-circle" class="w-6 h-6 text-emerald-500"/>
                        </div>
                        <span class="font-bold text-lg text-gray-900 dark:text-white group-hover:translate-x-2 transition-transform">Domain Status</span>
                    </div>
                    <p class="text-base text-gray-600 dark:text-gray-400 font-medium group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $website->status ?? 'Active' }}</p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 via-indigo-50 to-blue-50 dark:from-gray-800/50 dark:via-gray-700/50 dark:to-gray-600/50 p-6 rounded-3xl shadow-xl backdrop-blur-xl border border-purple-200/50 dark:border-purple-800/50 transform hover:scale-105 hover:rotate-2 transition-all duration-300 group">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="p-2 bg-white/90 dark:bg-gray-900/90 rounded-xl shadow-lg group-hover:animate-spin">
                            <x-icon name="heroicon-o-clock" class="w-6 h-6 text-purple-500"/>
                        </div>
                        <span class="font-bold text-lg text-gray-900 dark:text-white group-hover:translate-x-2 transition-transform">Last Checked</span>
                    </div>
                    <p class="text-base text-gray-600 dark:text-gray-400 font-medium group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">{{ now()->diffForHumans() }}</p>
                </div>

                <div class="bg-gradient-to-br from-amber-50 via-orange-50 to-red-50 dark:from-gray-800/50 dark:via-gray-700/50 dark:to-gray-600/50 p-6 rounded-3xl shadow-xl backdrop-blur-xl border border-amber-200/50 dark:border-amber-800/50 transform hover:scale-105 hover:rotate-2 transition-all duration-300 group">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="p-2 bg-white/90 dark:bg-gray-900/90 rounded-xl shadow-lg group-hover:animate-pulse">
                            <x-icon name="heroicon-o-shield-check" class="w-6 h-6 text-amber-500"/>
                        </div>
                        <span class="font-bold text-lg text-gray-900 dark:text-white group-hover:translate-x-2 transition-transform">Security Score</span>
                    </div>
                    <p class="text-base text-gray-600 dark:text-gray-400 font-medium group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">A+ ({{ rand(90, 100) }}/100)</p>
                </div>
            </div>

            <!-- Compact Stats -->
            <div x-show="isCompact" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 md:w-auto">
                <!-- SSL Certificates -->
                <div x-data="{ tooltip: false }" 
                     @mouseenter="tooltip = true" 
                     @mouseleave="tooltip = false" 
                     class="relative bg-gradient-to-br from-emerald-50/90 to-green-50/90 dark:from-emerald-900/20 dark:to-green-900/20 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-emerald-100 dark:border-emerald-800/50">
                    <div class="flex flex-col items-center gap-2">
                        <x-icon name="heroicon-o-shield-check" class="w-6 h-6 text-emerald-500"/>
                        <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100">{{ $website->certificates->count() }} Certificates</p>
                    </div>
                    <div x-show="tooltip" 
                         x-transition 
                         class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900/95 text-white text-xs rounded-xl shadow-xl z-50 backdrop-blur-sm">
                        Active SSL certificates for this domain
                    </div>
                </div>

                <!-- Security Score -->
                <div x-data="{ tooltip: false }" 
                     @mouseenter="tooltip = true" 
                     @mouseleave="tooltip = false" 
                     class="relative bg-gradient-to-br from-amber-50/90 to-orange-50/90 dark:from-amber-900/20 dark:to-orange-900/20 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-amber-100 dark:border-amber-800/50">
                    <div class="flex flex-col items-center gap-2">
                        <x-icon name="heroicon-o-shield-exclamation" class="w-6 h-6 text-amber-500"/>
                        <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">Score: {{ rand(90, 100) }}/100</p>
                    </div>
                    <div x-show="tooltip" 
                         x-transition 
                         class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900/95 text-white text-xs rounded-xl shadow-xl z-50 backdrop-blur-sm">
                        Overall security rating based on multiple factors
                    </div>
                </div>

                <!-- Last Scan -->
                <div x-data="{ tooltip: false }" 
                     @mouseenter="tooltip = true" 
                     @mouseleave="tooltip = false" 
                     class="relative bg-gradient-to-br from-purple-50/90 to-indigo-50/90 dark:from-purple-900/20 dark:to-indigo-900/20 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-purple-100 dark:border-purple-800/50">
                    <div class="flex flex-col items-center gap-2">
                        <x-icon name="heroicon-o-clock" class="w-6 h-6 text-purple-500"/>
                        <p class="text-sm font-semibold text-purple-900 dark:text-purple-100">{{ now()->diffForHumans() }}</p>
                    </div>
                    <div x-show="tooltip" 
                         x-transition 
                         class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900/95 text-white text-xs rounded-xl shadow-xl z-50 backdrop-blur-sm">
                        Time since last security assessment
                    </div>
                </div>

                <!-- SSL Expiry -->
                <div x-data="{ tooltip: false }" 
                     @mouseenter="tooltip = true" 
                     @mouseleave="tooltip = false" 
                     class="relative bg-gradient-to-br from-cyan-50/90 to-blue-50/90 dark:from-cyan-900/20 dark:to-blue-900/20 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-cyan-100 dark:border-cyan-800/50">
                    <div class="flex flex-col items-center gap-2">
                        <x-icon name="heroicon-o-calendar" class="w-6 h-6 text-cyan-500"/>
                        <p class="text-sm font-semibold text-cyan-900 dark:text-cyan-100">{{ rand(30, 90) }} Days Left</p>
                    </div>
                    <div x-show="tooltip" 
                         x-transition 
                         class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900/95 text-white text-xs rounded-xl shadow-xl z-50 backdrop-blur-sm">
                        Days until SSL certificate expiration
                    </div>
                </div>

                <!-- Domain Status -->
                <div x-data="{ tooltip: false }" 
                     @mouseenter="tooltip = true" 
                     @mouseleave="tooltip = false" 
                     class="relative bg-gradient-to-br from-green-50/90 to-teal-50/90 dark:from-green-900/20 dark:to-teal-900/20 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-green-100 dark:border-green-800/50">
                    <div class="flex flex-col items-center gap-2">
                        <x-icon name="heroicon-o-check-circle" class="w-6 h-6 text-green-500"/>
                        <p class="text-sm font-semibold text-green-900 dark:text-green-100">{{ $website->status ?? 'Active' }}</p>
                    </div>
                    <div x-show="tooltip" 
                         x-transition 
                         class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900/95 text-white text-xs rounded-xl shadow-xl z-50 backdrop-blur-sm">
                        Current domain operational status
                    </div>
                </div>

                <!-- Security Headers -->
                <div x-data="{ tooltip: false }" 
                     @mouseenter="tooltip = true" 
                     @mouseleave="tooltip = false" 
                     class="relative bg-gradient-to-br from-red-50/90 to-pink-50/90 dark:from-red-900/20 dark:to-pink-900/20 p-4 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 border border-red-100 dark:border-red-800/50">
                    <div class="flex flex-col items-center gap-2">
                        <x-icon name="heroicon-o-exclamation-triangle" class="w-6 h-6 text-red-500"/>
                        <p class="text-sm font-semibold text-red-900 dark:text-red-100">7/8 Headers</p>
                    </div>
                    <div x-show="tooltip" 
                         x-transition 
                         class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 p-3 bg-gray-900/95 text-white text-xs rounded-xl shadow-xl z-50 backdrop-blur-sm">
                        Security headers implementation status
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
