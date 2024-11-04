<div class="space-y-8">
    <!-- Security Overview Header -->
    <div class="mb-10 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-8 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
        <div class="relative flex items-center space-x-4">
            <div class="p-3 bg-white/20 rounded-xl">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-3xl font-bold">Security Analysis</h2>
                <p class="mt-2 text-blue-100">Comprehensive security assessment and recommendations for your domain</p>
                <div class="flex items-center mt-4 space-x-3">
                    <span class="px-3 py-1 text-sm bg-white/20 rounded-full">Last Scan: Today</span>
                    <span class="px-3 py-1 text-sm bg-white/20 rounded-full">{{ count($securityInsights['recommendations']) }} Issues Found</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Score Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transform hover:scale-[1.02] transition-all duration-300">
        <div class="flex items-center justify-between mb-8">
            <div class="flex-1">
                <div class="flex items-center space-x-3">
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Security Score</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Overall security rating based on multiple factors</p>
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">Headers</span>
                            <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">SSL/TLS</span>
                            <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">Vulnerabilities</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center">
                <div @class([
                    'px-8 py-4 rounded-xl text-3xl font-bold shadow-lg flex items-center space-x-2 relative overflow-hidden',
                    'bg-gradient-to-r from-green-400 to-green-500 text-white' => $securityInsights['security_score'] >= 80,
                    'bg-gradient-to-r from-yellow-400 to-yellow-500 text-white' => $securityInsights['security_score'] >= 60 && $securityInsights['security_score'] < 80,
                    'bg-gradient-to-r from-red-400 to-red-500 text-white' => $securityInsights['security_score'] < 60,
                ])>
                    <div class="absolute inset-0 bg-white/10 animate-pulse"></div>
                    <span class="relative">{{ $securityInsights['security_score'] }}</span>
                    <span class="relative text-lg opacity-75">/100</span>
                </div>
            </div>
        </div>

        <!-- Enhanced Progress Bar with Animation -->
        <div class="relative w-full bg-gray-200 rounded-full h-6 dark:bg-gray-700 overflow-hidden">
            <div @class([
                'h-6 rounded-full transition-all duration-1000 ease-out transform origin-left relative',
                'bg-gradient-to-r from-green-400 to-green-500' => $securityInsights['security_score'] >= 80,
                'bg-gradient-to-r from-yellow-400 to-yellow-500' => $securityInsights['security_score'] >= 60 && $securityInsights['security_score'] < 80,
                'bg-gradient-to-r from-red-400 to-red-500' => $securityInsights['security_score'] < 60,
            ]) style="width: {{ $securityInsights['security_score'] }}%">
                <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                <div class="absolute inset-0 flex items-center justify-center text-white text-sm font-medium">
                    {{ $securityInsights['security_score'] }}% Secure
                </div>
            </div>
        </div>

        <!-- Score Breakdown -->
        <div class="grid grid-cols-3 gap-4 mt-6">
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Headers Score</div>
                <div class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $securityInsights['headers_score'] ?? 0 }}%</div>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">SSL Score</div>
                <div class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $securityInsights['ssl_score'] ?? 0 }}%</div>
            </div>
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl text-center">
                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Vulnerability Score</div>
                <div class="mt-1 text-xl font-bold text-gray-900 dark:text-white">{{ $securityInsights['vulnerability_score'] ?? 0 }}%</div>
            </div>
        </div>
    </div>

    <!-- Security Recommendations -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl">
                    <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Security Recommendations</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Prioritized actions to improve security</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <span class="px-3 py-1 text-xs bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full">{{ count(array_filter($securityInsights['recommendations'], fn($r) => $r['severity'] === 'critical')) }} Critical</span>
                <span class="px-3 py-1 text-xs bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded-full">{{ count(array_filter($securityInsights['recommendations'], fn($r) => $r['severity'] === 'high')) }} High</span>
            </div>
        </div>
        
        <div class="space-y-4">
            @foreach($securityInsights['recommendations'] as $recommendation)
                <div @class([
                    'p-6 rounded-xl border transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1',
                    'bg-red-50/80 border-red-200 dark:bg-red-900/30 dark:border-red-800' => $recommendation['severity'] === 'critical',
                    'bg-orange-50/80 border-orange-200 dark:bg-orange-900/30 dark:border-orange-800' => $recommendation['severity'] === 'high',
                    'bg-yellow-50/80 border-yellow-200 dark:bg-yellow-900/30 dark:border-yellow-800' => $recommendation['severity'] === 'medium',
                    'bg-blue-50/80 border-blue-200 dark:bg-blue-900/30 dark:border-blue-800' => $recommendation['severity'] === 'low',
                ])>
                    <div class="flex items-start space-x-4">
                        <div @class([
                            'p-3 rounded-full',
                            'bg-red-100 text-red-700 dark:bg-red-900/50' => $recommendation['severity'] === 'critical',
                            'bg-orange-100 text-orange-700 dark:bg-orange-900/50' => $recommendation['severity'] === 'high',
                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50' => $recommendation['severity'] === 'medium',
                            'bg-blue-100 text-blue-700 dark:bg-blue-900/50' => $recommendation['severity'] === 'low',
                        ])>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span @class([
                                    'px-4 py-1.5 rounded-full text-sm font-semibold tracking-wide',
                                    'bg-red-100 text-red-800 dark:bg-red-900/70 dark:text-red-300' => $recommendation['severity'] === 'critical',
                                    'bg-orange-100 text-orange-800 dark:bg-orange-900/70 dark:text-orange-300' => $recommendation['severity'] === 'high',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/70 dark:text-yellow-300' => $recommendation['severity'] === 'medium',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/70 dark:text-blue-300' => $recommendation['severity'] === 'low',
                                ])>
                                    {{ ucfirst($recommendation['severity']) }}
                                </span>
                                <span class="text-base font-medium text-gray-900 dark:text-white">{{ $recommendation['message'] }}</span>
                            </div>
                            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Impact: {{ $recommendation['impact'] }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Priority: {{ ucfirst($recommendation['severity']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Security Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- SSL/TLS Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transform hover:scale-[1.02] transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-xl">
                        <svg class="w-7 h-7 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">SSL/TLS Configuration</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Certificate and encryption details</p>
                    </div>
                </div>
                @if($securityInsights['ssl']['valid'])
                    <span class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400 rounded-full text-sm">Valid</span>
                @else
                    <span class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400 rounded-full text-sm">Invalid</span>
                @endif
            </div>
            <div class="space-y-6">
                @if($securityInsights['ssl']['valid'])
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Issuer</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $securityInsights['ssl']['issuer'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Valid Until</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $securityInsights['ssl']['valid_to'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Protocol Version</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $securityInsights['ssl']['protocol_version'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Cipher Suite</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $securityInsights['ssl']['cipher_suite'] ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="flex items-center space-x-3 p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">SSL certificate is invalid or not found</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Security Headers -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-2xl border border-gray-200/50 dark:border-gray-700/50 transform hover:scale-[1.02] transition-all duration-300">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-xl">
                        <svg class="w-7 h-7 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Security Headers</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">HTTP security header configuration</p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-gray-100 text-gray-700 dark:bg-gray-700/50 dark:text-gray-400 rounded-full text-sm">
                    {{ count(array_filter($securityInsights['headers'])) }}/{{ count($securityInsights['headers']) }}
                </span>
            </div>
            <div class="space-y-4">
                @foreach($securityInsights['headers'] as $header => $value)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl group hover:bg-gray-100 dark:hover:bg-gray-600/50 transition-colors duration-200">
                        <div>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::title(str_replace('_', ' ', $header)) }}</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                {{ $securityInsights['header_descriptions'][$header] ?? 'Enhances security by controlling browser behavior' }}
                            </p>
                        </div>
                        <span @class([
                            'flex items-center space-x-2 px-3 py-1 rounded-full text-sm font-semibold',
                            'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-400' => $value,
                            'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-400' => !$value
                        ])>
                            @if($value)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Present</span>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span>Missing</span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
