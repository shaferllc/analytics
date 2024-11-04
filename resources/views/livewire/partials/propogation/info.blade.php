<div class="flex flex-col lg:flex-row gap-8">
    <!-- Main Content -->
    <div class="lg:w-3/4">
        <div class="bg-gradient-to-br from-white via-indigo-50 to-violet-100 overflow-hidden shadow-2xl sm:rounded-2xl border-2 border-indigo-200/50 hover:shadow-indigo-200/50 transition-all duration-300">
            <div class="p-10">
                <!-- Header -->
                <div class="mb-10 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-xl shadow-lg shadow-indigo-500/30">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600">DNS Propagation</h2>
                            <p class="text-sm text-indigo-600 mt-1 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Analysis across {{ $dns['propagation_data']['total_queries'] }} DNS providers
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-600 flex items-center justify-end gap-2">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            Domain Checked
                        </p>
                        <p class="text-lg font-bold text-indigo-600">{{ $dns['propagation_data']['domain_checked'] }}</p>
                        <p class="text-xs text-gray-500 mt-1 flex items-center justify-end gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ \Carbon\Carbon::parse($dns['propagation_data']['timestamp'])->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                    <!-- Success Rate -->
                    <div class="bg-white/90 backdrop-blur-lg rounded-xl p-6 shadow-lg border border-indigo-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-600">Success Rate</p>
                        </div>
                        <p class="text-2xl font-bold text-green-600">{{ number_format($dns['propagation_data']['success_rate'], 1) }}%</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $dns['propagation_data']['successful_queries'] }}/{{ $dns['propagation_data']['total_queries'] }} queries</p>
                    </div>
                    
                    <!-- Average Time -->
                    <div class="bg-white/90 backdrop-blur-lg rounded-xl p-6 shadow-lg border border-indigo-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 bg-indigo-100 rounded-lg">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-600">Average Time</p>
                        </div>
                        <p class="text-2xl font-bold text-indigo-600">{{ number_format($dns['propagation_data']['average_propagation_ms'], 1) }}ms</p>
                        <p class="text-xs text-gray-500 mt-1">Propagation time</p>
                    </div>

                    <!-- Min/Max Time -->
                    <div class="bg-white/90 backdrop-blur-lg rounded-xl p-6 shadow-lg border border-indigo-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 bg-violet-100 rounded-lg">
                                <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-600">Min/Max Time</p>
                        </div>
                        <p class="text-2xl font-bold text-violet-600">{{ number_format($dns['propagation_data']['min_propagation_ms']) }}ms</p>
                        <p class="text-xs text-gray-500 mt-1">to {{ number_format($dns['propagation_data']['max_propagation_ms']) }}ms</p>
                    </div>

                    <!-- Median Time -->
                    <div class="bg-white/90 backdrop-blur-lg rounded-xl p-6 shadow-lg border border-indigo-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="p-2 bg-fuchsia-100 rounded-lg">
                                <svg class="w-5 h-5 text-fuchsia-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-600">Median Time</p>
                        </div>
                        <p class="text-2xl font-bold text-fuchsia-600">{{ number_format($dns['propagation_data']['median_propagation_ms'], 1) }}ms</p>
                        <p class="text-xs text-gray-500 mt-1">50th percentile</p>
                    </div>
                </div>

                <!-- Provider Details -->
                <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-indigo-100 overflow-hidden">
                    <div class="grid grid-cols-1 divide-y divide-indigo-100">
                        @foreach($dns['propagation_data']['provider_times'] as $provider => $data)
                            <div x-data="{ expanded: false }" class="p-6 hover:bg-indigo-50/50 transition-colors duration-200">
                                <div class="flex items-center justify-between cursor-pointer" @click="expanded = !expanded">
                                    <div class="flex items-center gap-4">
                                        <div class="w-3 h-3 rounded-full {{ $data['status'] === 'success' ? 'bg-green-500 shadow-lg shadow-green-500/50' : 'bg-red-500 shadow-lg shadow-red-500/50' }} animate-pulse"></div>
                                        <div>
                                            <p class="font-semibold text-gray-900 capitalize flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                                </svg>
                                                {{ $provider }}
                                            </p>
                                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                                </svg>
                                                {{ $data['provider_type'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <p class="font-bold text-indigo-600">{{ number_format($data['propagation_time_ms'], 2) }}ms</p>
                                            <p class="text-xs text-gray-500">Score: {{ number_format($data['reliability_score'] * 100) }}%</p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                
                                <div x-show="expanded" x-collapse>
                                    @if($data['status'] === 'success')
                                        <div class="mt-3 text-sm text-gray-600 bg-indigo-50/50 rounded-xl p-4 border border-indigo-100">
                                            <p class="font-semibold mb-2 flex items-center gap-2">
                                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Response:
                                            </p>
                                            <pre class="text-xs whitespace-pre-wrap font-mono bg-white/50 rounded-lg p-3 border border-indigo-100">{{ $data['response'] }}</pre>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

              
            </div>
        </div>
    </div>
    <!-- Sidebar -->
    <div class="lg:w-1/4 space-y-8">
        <!-- Location Map -->
        <div x-data="{ showModal: false }">
            <div class="w-full bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border-2 border-indigo-100 p-6 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <h3 class="text-lg font-bold text-gray-900">Provider Locations</h3>
                    </div>
                    <button @click="showModal = true" class="text-indigo-500 hover:text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                        </svg>
                    </button>
                </div>
                <div class="w-full h-48 bg-gray-100 rounded-lg overflow-hidden">
                    <!-- Leaflet CSS -->
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
                          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
                          crossorigin=""/>
                    
                    <!-- Leaflet JavaScript -->
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
                            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
                            crossorigin=""></script>
                    <div id="map" class="w-full h-full" 
                         x-data="{ 
                             map: null,
                             initMap() {
                                 this.map = L.map('map').setView([20, 0], 2);
                                 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(this.map);
                                 
                                 <?php foreach($dns['propagation_data']['provider_times'] as $provider => $data): ?>
                                     <?php if(isset($data['location'])): ?>
                                         L.marker(
                                             [<?php echo $data['location']['lat']; ?>, <?php echo $data['location']['lon']; ?>]
                                         )
                                         .bindPopup(
                                             '<b><?php echo htmlspecialchars($provider); ?></b><br>' +
                                             '<p><?php echo htmlspecialchars($data['location']['city']); ?>, <?php echo htmlspecialchars($data['location']['country']); ?></p>' +
                                             '<p>Response: <?php echo htmlspecialchars(number_format($data['propagation_time_ms'], 2)); ?>ms</p>'
                                         )
                                         .addTo(this.map);
                                     <?php endif; ?>
                                 <?php endforeach; ?>
                             }
                         }"
                         x-init="initMap"
                    ></div>
                </div>
            </div>

            <!-- Modal -->
            <div x-show="showModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto" 
                 style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <!-- Overlay -->
                    <div class="fixed inset-0 bg-black opacity-30" @click="showModal = false"></div>

                    <!-- Modal content -->
                    <div class="relative bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl max-w-4xl w-full mx-auto p-8 border-2 border-indigo-100"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100"
                         x-transition:leave-end="opacity-0 transform scale-95">
                        
                        <!-- Close button -->
                        <button @click="showModal = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <h3 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600 mb-6 flex items-center gap-3">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Geographic Distribution
                        </h3>

                        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-4">
                            @php
                                $sortedProviders = collect($dns['propagation_data']['provider_times'])
                                    ->filter(fn($data) => isset($data['location']))
                                    ->sortBy('propagation_time_ms');
                            @endphp
                            @foreach($sortedProviders as $provider => $data)
                                <div class="group flex items-center justify-between p-5 bg-gradient-to-r from-white/80 to-indigo-50/80 rounded-2xl hover:shadow-xl hover:shadow-indigo-100/50 transition-all duration-300 border border-indigo-100/20 hover:border-indigo-200/50 hover:scale-[1.02] cursor-pointer">
                                    <div class="flex items-center gap-5">
                                        <div class="p-3.5 bg-gradient-to-br from-indigo-500/10 to-violet-500/10 rounded-xl group-hover:from-indigo-500/20 group-hover:to-violet-500/20 transition-all duration-300 shadow-lg shadow-indigo-100/20">
                                            <svg class="w-6 h-6 text-indigo-600 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <div class="group-hover:translate-x-1 transition-transform duration-300">
                                            <p class="text-sm font-bold text-gray-800 mb-0.5">{{ is_array($data['location']) ? implode(', ', $data['location']) : $data['location'] }}</p>
                                            <a href="{{ match($provider) {
                                                'cloudflare' => 'https://cloudflare.com',
                                                'google' => 'https://dns.google',
                                                'quad9' => 'https://quad9.net', 
                                                'opendns' => 'https://www.opendns.com',
                                                'level3' => 'https://www.lumen.com',
                                                'verisign' => 'https://www.verisign.com',
                                                'neustar' => 'https://www.home.neustar',
                                                'comodo' => 'https://www.comodo.com',
                                                'norton' => 'https://norton.com',
                                                'adguard' => 'https://adguard.com',
                                                'cleanbrowsing' => 'https://cleanbrowsing.org',
                                                'oneone' => 'https://1.1.1.1',
                                                'opennic' => 'https://www.opennic.org',
                                                'powerdns' => 'https://www.powerdns.com',
                                                'publicdns' => 'https://developers.google.com/speed/public-dns',
                                                'puredns' => 'https://puredns.org',
                                                'quad101' => 'https://101.101.101.101',
                                                'safedns' => 'https://www.safedns.com',
                                                'safesurfer' => 'https://safesurfer.io',
                                                'securedns' => 'https://securedns.eu',
                                                'smartviper' => 'https://www.smartviper.com',
                                                'uncensoreddns' => 'https://blog.uncensoreddns.org',
                                                'yandex' => 'https://dns.yandex.com',
                                                default => '#'
                                            } }}" target="_blank" class="text-xs text-indigo-600 font-semibold tracking-wide hover:text-indigo-800 transition-colors flex items-center gap-1">
                                                {{ $provider }}
                                                @if($provider !== '#')
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                    </svg>
                                                @endif
                                            </a>
                                        </div>
                                    </div>
                                    <div class="text-right group-hover:-translate-x-1 transition-transform duration-300">
                                        <p class="text-base font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-violet-600 mb-0.5">{{ number_format($data['propagation_time_ms'], 1) }}ms</p>
                                        <p class="text-xs text-indigo-400 font-medium uppercase tracking-wider">Response Time</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Response Time Distribution -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border-2 border-indigo-100 p-6 hover:shadow-2xl transition-all duration-300">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Response Time Distribution
            </h3>
            <div class="space-y-3">
                @php
                    $ranges = [
                        '0-50ms' => 0,
                        '51-100ms' => 0,
                        '101-500ms' => 0,
                        '500ms+' => 0
                    ];
                    foreach($dns['propagation_data']['provider_times'] as $data) {
                        $time = $data['propagation_time_ms'];
                        if($time <= 50) $ranges['0-50ms']++;
                        elseif($time <= 100) $ranges['51-100ms']++;
                        elseif($time <= 500) $ranges['101-500ms']++;
                        else $ranges['500ms+']++;
                    }
                @endphp
                @foreach($ranges as $range => $count)
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600 font-medium">{{ $range }}</span>
                            <span class="font-semibold text-indigo-600">{{ $count }}</span>
                        </div>
                        <div class="h-2.5 bg-indigo-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-indigo-500 to-violet-500 rounded-full shadow-lg transition-all duration-500" style="width: {{ ($count / count($dns['propagation_data']['provider_times'])) * 100 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Provider Types -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border-2 border-indigo-100 p-6 hover:shadow-2xl transition-all duration-300">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Provider Types
            </h3>
            <div class="space-y-4">
                @php
                    $providerTypes = [];
                    foreach($dns['propagation_data']['provider_times'] as $data) {
                        $type = $data['provider_type'];
                        $providerTypes[$type] = ($providerTypes[$type] ?? 0) + 1;
                    }
                @endphp
                @foreach($providerTypes as $type => $count)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 font-medium">{{ $type }}</span>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-600 rounded-full text-xs font-semibold">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Health Score -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border-2 border-indigo-100 p-6 hover:shadow-2xl transition-all duration-300">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                DNS Health Score
            </h3>
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-violet-500 mb-4">
                    <span class="text-2xl font-bold text-white">{{ $dns['stats']['health_score'] }}%</span>
                </div>
                <p class="text-sm text-gray-600">Based on propagation success rate, response times, and DNS configuration</p>
            </div>
        </div>
    </div>
    <!-- Sidebar -->
    <div class="lg:w-1/4 space-y-6">
        <!-- Quick Stats -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border-2 border-indigo-100 p-6 hover:shadow-2xl transition-all duration-300">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Quick Stats
            </h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600">Average Propagation Time</p>
                    <p class="text-xl font-bold text-indigo-600">{{ $dns['propagation_data']['average_propagation_ms'] }}ms</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Success Rate</p>
                    <p class="text-xl font-bold text-indigo-600">{{ $dns['propagation_data']['success_rate'] }}%</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total DNS Providers</p>
                    <p class="text-xl font-bold text-indigo-600">{{ $dns['propagation_data']['total_queries'] }}</p>
                </div>
            </div>
        </div>

        <!-- Response Time Range -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border-2 border-indigo-100 p-6 hover:shadow-2xl transition-all duration-300">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Response Time Range
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600">Fastest Response</p>
                    <p class="text-lg font-semibold text-green-600">{{ $dns['propagation_data']['min_propagation_ms'] }}ms</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Median Response</p>
                    <p class="text-lg font-semibold text-indigo-600">{{ $dns['propagation_data']['median_propagation_ms'] }}ms</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Slowest Response</p>
                    <p class="text-lg font-semibold text-red-600">{{ $dns['propagation_data']['max_propagation_ms'] }}ms</p>
                </div>
            </div>
        </div>

        <!-- Last Updated -->
        <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border-2 border-indigo-100 p-6 hover:shadow-2xl transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-600">Last Updated</h3>
                    <p class="text-lg font-bold text-indigo-600">{{ \Carbon\Carbon::parse($dns['propagation_data']['timestamp'])->format('M j, Y g:i A') }}</p>
                </div>
                <button wire:click="refresh" class="p-2 bg-indigo-100 rounded-lg text-indigo-600 hover:bg-indigo-200 transition-colors duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
</div>
