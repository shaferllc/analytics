@if(!$robotsAnalysis)
    <div class="p-8 bg-red-50 dark:bg-red-900/30 rounded-2xl">
        <p class="text-red-600 dark:text-red-400">No robots.txt analysis data available</p>
    </div>
@else
<div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
    <!-- Robots.txt Analysis -->
    <div class="xl:col-span-8 bg-gradient-to-br from-white/70 to-blue-50/50 dark:from-gray-800/70 dark:to-blue-900/30 rounded-2xl p-8 backdrop-blur-lg border border-indigo-200 dark:border-indigo-800 shadow-xl hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center gap-3 mb-6">
            <div class="p-3 bg-gradient-to-br from-blue-500 via-indigo-500 to-purple-600 rounded-xl shadow-lg shadow-blue-500/20 animate-gradient-x">
                <x-icon name="heroicon-o-code-bracket-square" class="w-6 h-6 text-white animate-pulse"/>
            </div>
            <div>
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">Robots.txt Analysis</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                    <x-icon name="heroicon-o-clock" class="w-4 h-4"/>
                    Last analyzed: {{ isset($robotsAnalysis['analysis_timestamp']) ? Carbon\Carbon::parse($robotsAnalysis['analysis_timestamp'])->diffForHumans() : 'Never' }}
                </p>
            </div>
            @if(isset($robotsAnalysis['score']))
            <div class="ml-auto">
                <div class="text-2xl font-bold {{ $robotsAnalysis['score'] >= 80 ? 'text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-green-500' : ($robotsAnalysis['score'] >= 60 ? 'text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500' : 'text-transparent bg-clip-text bg-gradient-to-r from-red-400 to-pink-500') }}">
                    {{ $robotsAnalysis['score'] }}/100
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    @if($robotsAnalysis['score'] >= 80)
                        Great robots.txt configuration!
                    @elseif($robotsAnalysis['score'] >= 60)
                        Some improvements needed
                    @else
                        Major improvements required
                    @endif
                    <div x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="relative inline">
                        <x-icon name="heroicon-o-information-circle" class="w-4 h-4 inline ml-1 cursor-help"/>
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-1"
                             class="absolute z-[100] w-72 px-4 py-3 text-sm bg-gray-900 text-gray-100 dark:bg-gray-800 rounded-lg shadow-lg -bottom-[280px] -left-2"
                             style="display: none;">
                            Score based on:
                            <ul class="list-disc pl-4 mt-1">
                                <li>Major bot coverage (-5 per missing bot)</li>
                                <li>Security measures (-5)</li>
                                <li>SEO optimization (-10)</li>
                                <li>Performance directives (-5)</li>
                                <li>Recommended path rules (-2)</li>
                                <li>File extension rules (-1)</li>
                                <li>Crawl delay settings (-3)</li>
                                <li>Request rate limits (-2)</li>
                                <li>Clean parameter usage (-1)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- User Agents -->
        <div class="space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <x-icon name="heroicon-o-user-group" class="w-5 h-5 text-indigo-500"/>
                    User Agents
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($robotsAnalysis['user-agents'] ?? [] as $agent => $rules)
                        <div class="p-4 bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900/30 dark:to-blue-900/20 rounded-xl hover:shadow-md transition-all duration-300">
                            <p class="font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2 2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
                                    <path d="M4 9h16a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4a2 2 0 0 1 2-2z"/>
                                    <path d="M8 16v2a2 2 0 0 0 2 2h4a2 2 0 0 0 2-2v-2"/>
                                    <circle cx="8" cy="12" r="1"/>
                                    <circle cx="16" cy="12" r="1"/>
                                </svg>
                                {{ $agent }}
                            </p>
                            <div class="mt-2 space-y-1 text-sm">
                                <p class="text-gray-500 flex items-center gap-2">
                                    <x-icon name="heroicon-o-clock" class="w-4 h-4 text-purple-400"/>
                                    Crawl Delay: {{ $rules['crawl-delay'] ?? 'Not set' }}
                                </p>
                                <p class="text-gray-500 flex items-center gap-2">
                                    <x-icon name="heroicon-o-list-bullet" class="w-4 h-4 text-indigo-400"/>
                                    Rules: {{ count($rules['allow'] ?? []) + count($rules['disallow'] ?? []) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Global Rules -->
            <div class="space-y-4">
                <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                    <x-icon name="heroicon-o-globe-alt" class="w-5 h-5 text-blue-500"/>
                    Global Rules
                </h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Allow Rules -->
                    <div class="p-4 bg-gradient-to-br from-gray-50 to-green-50 dark:from-gray-900/30 dark:to-green-900/20 rounded-xl">
                        <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <x-icon name="heroicon-o-check-circle" class="w-4 h-4 text-green-500"/>
                            Allow Rules
                        </h5>
                        @forelse($robotsAnalysis['allow'] ?? [] as $rule)
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $rule }}</p>
                        @empty
                            <p class="text-sm text-gray-500">No allow rules defined</p>
                        @endforelse
                    </div>

                    <!-- Disallow Rules -->
                    <div class="p-4 bg-gradient-to-br from-gray-50 to-red-50 dark:from-gray-900/30 dark:to-red-900/20 rounded-xl">
                        <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-2 flex items-center gap-2">
                            <x-icon name="heroicon-o-x-circle" class="w-4 h-4 text-red-500"/>
                            Disallow Rules
                        </h5>
                        @forelse($robotsAnalysis['disallow'] ?? [] as $rule)
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $rule }}</p>
                        @empty
                            <p class="text-sm text-gray-500">No disallow rules defined</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sitemaps & Additional Settings -->
    <div class="xl:col-span-4 space-y-8">
        <!-- Sitemaps -->
        <div class="bg-gradient-to-br from-white/70 via-green-50/30 to-emerald-50/30 dark:from-gray-800/70 dark:via-green-900/20 dark:to-emerald-900/20 rounded-2xl p-8 backdrop-blur-lg border border-emerald-200 dark:border-emerald-800 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-gradient-to-br from-green-500 via-emerald-500 to-teal-600 rounded-xl shadow-lg shadow-green-500/20 animate-gradient-x">
                    <x-icon name="heroicon-o-map" class="w-6 h-6 text-white animate-pulse"/>
                </div>
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-400 dark:to-emerald-400">Sitemaps</h3>
            </div>
            
            @if(!empty($robotsAnalysis['sitemaps']))
                <div class="space-y-3">
                    @foreach($robotsAnalysis['sitemaps'] as $sitemap)
                        <div class="flex items-center gap-3 p-4 bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/30 dark:to-green-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                            <x-icon name="heroicon-o-document-text" class="w-5 h-5 text-emerald-500"/>
                            <span class="text-gray-700 dark:text-gray-300 text-sm break-all">{{ $sitemap }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-4 bg-gradient-to-br from-gray-50 to-emerald-50/30 dark:from-gray-900/30 dark:to-emerald-900/20 rounded-xl">
                    <p class="text-gray-500 dark:text-gray-400 flex items-center gap-2">
                        <x-icon name="heroicon-o-x-circle" class="w-5 h-5"/>
                        No sitemaps defined
                    </p>
                </div>
            @endif
        </div>

        <!-- Additional Settings -->
        <div class="bg-gradient-to-br from-white/70 via-purple-50/30 to-indigo-50/30 dark:from-gray-800/70 dark:via-purple-900/20 dark:to-indigo-900/20 rounded-2xl p-8 backdrop-blur-lg border border-purple-200 dark:border-purple-800 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-gradient-to-br from-purple-500 via-indigo-500 to-violet-600 rounded-xl shadow-lg shadow-purple-500/20 animate-gradient-x">
                    <x-icon name="heroicon-o-adjustments-horizontal" class="w-6 h-6 text-white animate-pulse"/>
                </div>
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600 dark:from-purple-400 dark:to-indigo-400">Additional Settings</h3>
            </div>

            <div class="space-y-4">
                <!-- Host -->
                <div class="p-4 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/30 dark:to-indigo-900/30 rounded-xl">
                    <p class="text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-server" class="w-4 h-4 text-purple-500"/>
                        Host: {{ $robotsAnalysis['host'] ?? 'Not set' }}
                    </p>
                </div>

                <!-- Visit Time -->
                <div class="p-4 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/30 dark:to-indigo-900/30 rounded-xl">
                    <p class="text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-clock" class="w-4 h-4 text-purple-500"/>
                        Visit Time: {{ $robotsAnalysis['visit-time'] ?? 'Not set' }}
                    </p>
                </div>

                <!-- Request Rate -->
                <div class="p-4 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/30 dark:to-indigo-900/30 rounded-xl">
                    <p class="text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 text-purple-500"/>
                        Request Rate: {{ $robotsAnalysis['request-rate'] ?? 'Not set' }}
                    </p>
                </div>

                <!-- Clean Parameters -->
                <div class="p-4 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/30 dark:to-indigo-900/30 rounded-xl">
                    <p class="text-gray-700 dark:text-gray-300 flex items-center gap-2 mb-2">
                        <x-icon name="heroicon-o-variable" class="w-4 h-4 text-purple-500"/>
                        Clean Parameters
                    </p>
                    @if(!empty($robotsAnalysis['clean-param']))
                        @foreach($robotsAnalysis['clean-param'] as $param)
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $param }}</p>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">No clean parameters defined</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Improvements Section -->
    <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-amber-50/30 to-yellow-50/30 dark:from-gray-800/70 dark:via-amber-900/20 dark:to-yellow-900/20 rounded-2xl p-8 backdrop-blur-lg border border-amber-200 dark:border-amber-800 shadow-xl hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-amber-500 via-yellow-500 to-orange-600 rounded-xl shadow-lg shadow-amber-500/20 animate-gradient-x">
                    <x-icon name="heroicon-o-light-bulb" class="w-6 h-6 text-white animate-pulse"/>
                </div>
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-amber-600 to-yellow-600 dark:from-amber-400 dark:to-yellow-400">Suggested Improvements</h3>
            </div>
            <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
            </button>
        </div>

        <div x-show="isOpen" x-collapse class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($robotsAnalysis['improvements'] ?? [] as $improvement)
                <div class="p-4 bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/30 dark:to-yellow-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                    <p class="text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-sparkles" class="w-4 h-4 text-amber-500"/>
                        {{ $improvement['description'] }}
                    </p>
                    @if(isset($improvement['example']))
                        <div x-data="{ copied: false }" class="relative mt-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400 italic bg-blue-50/50 dark:bg-blue-900/30 p-3 rounded-xl border-2 border-blue-200 dark:border-blue-700 shadow-sm">
                                {{ $improvement['example'] }}
                                <button @click="navigator.clipboard.writeText('{{ $improvement['example'] }}'); copied = true; setTimeout(() => copied = false, 2000)" class="absolute right-3 top-3 text-blue-500 dark:text-blue-300 hover:text-blue-600 dark:hover:text-blue-200 transition-colors duration-200">
                                    <x-icon name="heroicon-o-clipboard" class="w-4 h-4" x-show="!copied"/>
                                    <x-icon name="heroicon-o-check" class="w-4 h-4 text-emerald-500 dark:text-emerald-300" x-show="copied"/>
                                </button>
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Missing Bots Section -->
    <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-red-50/30 to-pink-50/30 dark:from-gray-800/70 dark:via-red-900/20 dark:to-pink-900/20 rounded-2xl p-8 backdrop-blur-lg border border-red-200 dark:border-red-800 shadow-xl hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-red-500 via-pink-500 to-rose-600 rounded-xl shadow-lg shadow-red-500/20 animate-gradient-x">
                    <x-icon name="heroicon-o-exclamation-triangle" class="w-6 h-6 text-white animate-pulse"/>
                </div>
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-pink-600 dark:from-red-400 dark:to-pink-400">Missing Bot Rules</h3>
            </div>
            <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
            </button>
        </div>

        <div x-show="isOpen" x-collapse class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach(collect($robotsAnalysis['recommendations']['missing_bots'] ?? [])->sortBy('name') as $bot)
                <div class="p-4 bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/30 dark:to-pink-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                    <div class="flex flex-col gap-2">
                        <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $bot['name'] }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $bot['suggestion'] }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-1 text-xs rounded-full {{ $bot['importance'] === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300' : 'bg-pink-100 text-pink-700 dark:bg-pink-900/50 dark:text-pink-300' }}">
                                {{ ucfirst($bot['importance']) }} Priority
                            </span>
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                                {{ $bot['impact'] }}
                            </span>
                            @if(isset($bot['example']))
                                <div x-data="{ showRules: false }" class="relative">
                                    <button @click="showRules = !showRules" class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300 flex items-center gap-1">
                                        <x-icon name="heroicon-o-code-bracket" class="w-3 h-3"/>
                                        Rules
                                    </button>
                                    <div x-show="showRules" @click.away="showRules = false" class="absolute left-0 mt-2 p-3 bg-white dark:bg-gray-800 rounded-lg shadow-lg z-10 w-64">
                                        @if(!empty($bot['example']['allow']))
                                            <div class="mb-2">
                                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Allow:</span>
                                                @foreach($bot['example']['allow'] as $allow)
                                                    <div class="text-xs text-emerald-600 dark:text-emerald-400">{{ $allow }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(!empty($bot['example']['disallow']))
                                            <div class="mb-2">
                                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Disallow:</span>
                                                @foreach($bot['example']['disallow'] as $disallow)
                                                    <div class="text-xs text-red-600 dark:text-red-400">{{ $disallow }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if(isset($bot['example']['crawl-delay']))
                                            <div class="mb-2">
                                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Crawl-delay:</span>
                                                <div class="text-xs text-blue-600 dark:text-blue-400">{{ $bot['example']['crawl-delay'] }}</div>
                                            </div>
                                        @endif
                                        @if(isset($bot['example']['request-rate']))
                                            <div>
                                                <span class="text-xs font-semibold text-gray-600 dark:text-gray-400">Request-rate:</span>
                                                <div class="text-xs text-purple-600 dark:text-purple-400">{{ $bot['example']['request-rate'] }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Suggested Disallow Section -->
    <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-purple-50/30 to-indigo-50/30 dark:from-gray-800/70 dark:via-purple-900/20 dark:to-indigo-900/20 rounded-2xl p-8 backdrop-blur-lg border border-purple-200 dark:border-purple-800 shadow-xl hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-purple-500 via-indigo-500 to-violet-600 rounded-xl shadow-lg shadow-purple-500/20 animate-gradient-x">
                    <x-icon name="heroicon-o-no-symbol" class="w-6 h-6 text-white animate-pulse"/>
                </div>
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-indigo-600 dark:from-purple-400 dark:to-indigo-400">Suggested Disallow Rules</h3>
            </div>
            <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
            </button>
        </div>

        <div x-show="isOpen" x-collapse>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($robotsAnalysis['recommendations']['suggested_disallow'] ?? [] as $disallow)
                    <div class="p-4 bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/30 dark:to-indigo-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                        <div class="flex flex-col gap-2">
                            <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $disallow['path'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $disallow['reason'] }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-1 text-xs rounded-full {{ $disallow['importance'] === 'high' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' }}">
                                    {{ ucfirst($disallow['importance']) }} Priority
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300">
                                    {{ $disallow['impact'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if(count($robotsAnalysis['recommendations']['suggested_crawl_delays'] ?? []) > 0)
    <!-- Suggested Crawl Delays Section -->
        <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-emerald-50/30 to-green-50/30 dark:from-gray-800/70 dark:via-emerald-900/20 dark:to-green-900/20 rounded-2xl p-8 backdrop-blur-lg border border-emerald-200 dark:border-emerald-800 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl shadow-lg shadow-emerald-500/20 animate-gradient-x">
                        <x-icon name="heroicon-o-clock" class="w-6 h-6 text-white animate-pulse"/>
                    </div>
                    <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-green-600 dark:from-emerald-400 dark:to-green-400">Suggested Crawl Delays</h3>
                </div>
                <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                    <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                    <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
                </button>
            </div>

            <div x-show="isOpen" x-collapse>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($robotsAnalysis['recommendations']['suggested_crawl_delays'] ?? [] as $delay)
                        <div class="p-4 bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/30 dark:to-green-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col gap-2">
                                <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $delay['bot'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Suggested delay: {{ $delay['delay'] }} seconds</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $delay['reason'] }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $delay['importance'] === 'high' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' }}">
                                        {{ ucfirst($delay['importance']) }} Priority
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300">
                                        {{ $delay['impact'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Security Improvements Section -->
    @if(count($robotsAnalysis['recommendations']['security_improvements'] ?? []) > 0)
        <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-red-50/30 to-rose-50/30 dark:from-gray-800/70 dark:via-red-900/20 dark:to-rose-900/20 rounded-2xl p-8 backdrop-blur-lg border border-red-200 dark:border-red-800 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-br from-red-500 via-rose-500 to-pink-600 rounded-xl shadow-lg shadow-red-500/20 animate-gradient-x">
                        <x-icon name="heroicon-o-shield-check" class="w-6 h-6 text-white animate-pulse"/>
                    </div>
                    <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-red-600 to-rose-600 dark:from-red-400 dark:to-rose-400">Security Improvements</h3>
                </div>
                <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                    <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                    <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
                </button>
            </div>

            <div x-show="isOpen" x-collapse>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($robotsAnalysis['recommendations']['security_improvements'] ?? [] as $security)
                        <div class="p-4 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-900/30 dark:to-rose-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col gap-2">
                                <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $security['type'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $security['suggestion'] }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $security['importance'] === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300' }}">
                                        {{ ucfirst($security['importance']) }} Priority
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-pink-100 text-pink-700 dark:bg-pink-900/50 dark:text-pink-300">
                                        {{ $security['impact'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- SEO Improvements Section -->
    @if(count($robotsAnalysis['recommendations']['seo_improvements'] ?? []) > 0)
        <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-blue-50/30 to-indigo-50/30 dark:from-gray-800/70 dark:via-blue-900/20 dark:to-indigo-900/20 rounded-2xl p-8 backdrop-blur-lg border border-blue-200 dark:border-blue-800 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-br from-blue-500 via-indigo-500 to-violet-600 rounded-xl shadow-lg shadow-blue-500/20 animate-gradient-x">
                        <x-icon name="heroicon-o-magnifying-glass" class="w-6 h-6 text-white animate-pulse"/>
                    </div>
                    <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400">SEO Improvements</h3>
                </div>
                <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                    <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                    <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
                </button>
            </div>

            <div x-show="isOpen" x-collapse>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($robotsAnalysis['recommendations']['seo_improvements'] ?? [] as $seo)
                        <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col gap-2">
                                <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $seo['type'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $seo['suggestion'] }}</p>
                                @if(isset($seo['details']))
                                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ $seo['details'] }}</p>
                                @endif
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $seo['importance'] === 'high' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' }}">
                                        {{ ucfirst($seo['importance']) }} Priority
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300">
                                        {{ $seo['impact'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Performance Improvements Section -->
    @if(count($robotsAnalysis['recommendations']['performance_improvements'] ?? []) > 0)
        <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-green-50/30 to-emerald-50/30 dark:from-gray-800/70 dark:via-green-900/20 dark:to-emerald-900/20 rounded-2xl p-8 backdrop-blur-lg border border-green-200 dark:border-green-800 shadow-xl hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="p-3 bg-gradient-to-br from-green-500 via-emerald-500 to-teal-600 rounded-xl shadow-lg shadow-green-500/20 animate-gradient-x">
                        <x-icon name="heroicon-o-bolt" class="w-6 h-6 text-white animate-pulse"/>
                    </div>
                    <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-400 dark:to-emerald-400">Performance Improvements</h3>
                </div>
                <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                    <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                    <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
                </button>
            </div>

            <div x-show="isOpen" x-collapse>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($robotsAnalysis['recommendations']['performance_improvements'] ?? [] as $performance)
                        <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col gap-2">
                                <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $performance['type'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $performance['suggestion'] }}</p>
                                @if(isset($performance['details']))
                                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ $performance['details'] }}</p>
                                @endif
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $performance['importance'] === 'high' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' }}">
                                        {{ ucfirst($performance['importance']) }} Priority
                                    </span>
                                    <span class="px-2 py-1 text-xs rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300">
                                        {{ $performance['impact'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Clean Parameters Section -->
    <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-yellow-50/30 to-amber-50/30 dark:from-gray-800/70 dark:via-yellow-900/20 dark:to-amber-900/20 rounded-2xl p-8 backdrop-blur-lg border border-yellow-200 dark:border-yellow-800 shadow-xl hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-yellow-500 via-amber-500 to-orange-600 rounded-xl shadow-lg shadow-yellow-500/20 animate-gradient-x">
                    <x-icon name="heroicon-o-funnel" class="w-6 h-6 text-white animate-pulse"/>
                </div>
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-yellow-600 to-amber-600 dark:from-yellow-400 dark:to-amber-400">Suggested Clean Parameters</h3>
            </div>
            <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
            </button>
        </div>

        <div x-show="isOpen" x-collapse>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($robotsAnalysis['recommendations']['suggested_clean_params'] ?? [] as $param)
                    <div class="p-4 bg-gradient-to-br from-yellow-50 to-amber-50 dark:from-yellow-900/30 dark:to-amber-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                        <div class="flex flex-col gap-2">
                            <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $param['param'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $param['suggestion'] }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-1 text-xs rounded-full {{ $param['importance'] === 'high' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300' }}">
                                    {{ ucfirst($param['importance']) }} Priority
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300">
                                    {{ $param['impact'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Request Rates Section -->
    <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-purple-50/30 to-violet-50/30 dark:from-gray-800/70 dark:via-purple-900/20 dark:to-violet-900/20 rounded-2xl p-8 backdrop-blur-lg border border-purple-200 dark:border-purple-800 shadow-xl hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-purple-500 via-violet-500 to-indigo-600 rounded-xl shadow-lg shadow-purple-500/20 animate-gradient-x">
                    <x-icon name="heroicon-o-clock" class="w-6 h-6 text-white animate-pulse"/>
                </div>
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-violet-600 dark:from-purple-400 dark:to-violet-400">Suggested Request Rates</h3>
            </div>
            <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
            </button>
        </div>

        <div x-show="isOpen" x-collapse>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($robotsAnalysis['recommendations']['suggested_request_rates'] ?? [] as $rate)
                    <div class="p-4 bg-gradient-to-br from-purple-50 to-violet-50 dark:from-purple-900/30 dark:to-violet-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                        <div class="flex flex-col gap-2">
                            <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $rate['bot'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $rate['suggestion'] }}</p>
                            @if(isset($rate['details']))
                                <p class="text-xs text-gray-500 dark:text-gray-500">{{ $rate['details'] }}</p>
                            @endif
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-1 text-xs rounded-full {{ $rate['importance'] === 'high' ? 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300' : 'bg-violet-100 text-violet-700 dark:bg-violet-900/50 dark:text-violet-300' }}">
                                    {{ ucfirst($rate['importance']) }} Priority
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300">
                                    {{ $rate['impact'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- File Extension Rules Section -->
    <div x-data="{ isOpen: true }" class="xl:col-span-12 bg-gradient-to-br from-white/70 via-emerald-50/30 to-green-50/30 dark:from-gray-800/70 dark:via-emerald-900/20 dark:to-green-900/20 rounded-2xl p-8 backdrop-blur-lg border border-emerald-200 dark:border-emerald-800 shadow-xl hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl shadow-lg shadow-emerald-500/20 animate-gradient-x">
                    <x-icon name="heroicon-o-document" class="w-6 h-6 text-white animate-pulse"/>
                </div>
                <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-green-600 dark:from-emerald-400 dark:to-green-400">File Extension Rules</h3>
            </div>
            <button @click="isOpen = !isOpen" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200">
                <x-icon name="heroicon-o-chevron-up" class="w-6 h-6" x-show="isOpen"/>
                <x-icon name="heroicon-o-chevron-down" class="w-6 h-6" x-show="!isOpen"/>
            </button>
        </div>

        <div x-show="isOpen" x-collapse>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($robotsAnalysis['recommendations']['file_extension_rules'] ?? [] as $rule)
                    <div class="p-4 bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/30 dark:to-green-900/30 rounded-xl hover:shadow-md transition-all duration-300">
                        <div class="flex flex-col gap-2">
                            <p class="text-gray-700 dark:text-gray-300 font-semibold">{{ $rule['extension'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $rule['suggestion'] }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-1 text-xs rounded-full {{ $rule['importance'] === 'high' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' }}">
                                    {{ ucfirst($rule['importance']) }} Priority
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-teal-100 text-teal-700 dark:bg-teal-900/50 dark:text-teal-300">
                                    {{ $rule['impact'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
