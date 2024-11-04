<div class="flex flex-col lg:flex-row gap-8">
    <!-- Main Content -->
    <div class="lg:w-3/4">
        <div class="bg-gradient-to-br from-white via-indigo-50 to-violet-100 overflow-hidden shadow-2xl sm:rounded-2xl border-2 border-indigo-200/50 hover:shadow-indigo-200/50 transition-all duration-300">
            <div class="p-10">
                <!-- Header with Last/Next Check Times -->
                <div class="mb-10 flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-gradient-to-br from-indigo-500 to-violet-500 rounded-xl shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.25 16.75L14.5 12l4.75-4.75M4.75 12h9.5" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600">DNS Overview</h2>
                            <p class="text-sm text-indigo-600 mt-1">Comprehensive analysis of your domain's DNS configuration</p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-3 text-sm bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white px-6 py-3 rounded-xl shadow-lg hover:scale-105 transition-transform">
                            <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <span class="block font-semibold">Last checked</span>
                                <span class="text-xs opacity-90">{{ \Carbon\Carbon::parse($dns['last_check'])->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 text-sm bg-gradient-to-r from-pink-600 via-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl shadow-lg hover:scale-105 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <span class="block font-semibold">Next check</span>
                                <span class="text-xs opacity-90">{{ \Carbon\Carbon::parse($dns['last_check'])->addHours(24)->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 mb-12">
                    <!-- Total Records -->
                    <div class="bg-gradient-to-br from-cyan-100 via-blue-50 to-white border-2 border-cyan-200/50 rounded-2xl p-8 shadow-xl transform hover:scale-105 transition-all duration-300 hover:shadow-cyan-100">
                        <div class="flex items-center gap-5">
                            <div class="p-4 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-xl shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-cyan-700 uppercase tracking-wider mb-1">Total Records</p>
                                <p class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-600 to-blue-600">{{ number_format($dns['stats']['total_records']) }}</p>
                                <p class="text-xs text-cyan-600 mt-1">Across all record types</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-cyan-200">
                            <p class="text-xs text-cyan-700">
                                <span class="font-semibold">Distribution:</span> 
                                {{ round(($dns['stats']['total_records'] / 100), 1) }} records per subdomain
                            </p>
                        </div>
                    </div>

                    <!-- Health Score -->
                    <div class="bg-gradient-to-br from-{{ $dns['stats']['health_score'] > 50 ? 'emerald' : 'rose' }}-100 via-{{ $dns['stats']['health_score'] > 50 ? 'teal' : 'pink' }}-50 to-white border-2 border-{{ $dns['stats']['health_score'] > 50 ? 'emerald' : 'rose' }}-200/50 rounded-2xl p-8 shadow-xl transform hover:scale-105 transition-all duration-300 hover:shadow-{{ $dns['stats']['health_score'] > 50 ? 'emerald' : 'rose' }}-100">
                        <div class="flex items-center gap-5">
                            <div class="p-4 bg-gradient-to-br from-{{ $dns['stats']['health_score'] > 50 ? 'emerald-400' : 'rose-400' }} to-{{ $dns['stats']['health_score'] > 50 ? 'teal-500' : 'pink-500' }} rounded-xl shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-{{ $dns['stats']['health_score'] > 50 ? 'emerald' : 'rose' }}-700 uppercase tracking-wider mb-1">Health Score</p>
                                <p class="text-4xl font-black text-{{ $dns['stats']['health_score'] > 50 ? 'emerald-600' : 'rose-600' }}">{{ $dns['stats']['health_score'] }}%</p>
                                <p class="text-xs text-{{ $dns['stats']['health_score'] > 50 ? 'emerald' : 'rose' }}-600 mt-1">
                                    {{ $dns['stats']['health_score'] > 75 ? 'Excellent' : ($dns['stats']['health_score'] > 50 ? 'Good' : 'Needs Attention') }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-{{ $dns['stats']['health_score'] > 50 ? 'emerald' : 'rose' }}-200">
                            <div class="h-2 bg-gray-100 rounded-full">
                                <div class="h-2 {{ $dns['stats']['health_score'] > 50 ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-full animate-pulse" style="width: {{ $dns['stats']['health_score'] }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Changes -->
                    <div class="bg-gradient-to-br from-fuchsia-100 via-purple-50 to-white border-2 border-fuchsia-200/50 rounded-2xl p-8 shadow-xl transform hover:scale-105 transition-all duration-300 hover:shadow-fuchsia-100">
                        <div class="flex items-center gap-5">
                            <div class="p-4 bg-gradient-to-br from-fuchsia-400 to-purple-500 rounded-xl shadow-lg">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-fuchsia-700 uppercase tracking-wider mb-1">24h Changes</p>
                                <p class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-fuchsia-600 to-purple-600">{{ number_format($dns['stats']['record_changes_24h']) }}</p>
                                <p class="text-xs text-fuchsia-600 mt-1">Record modifications</p>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-fuchsia-200">
                            <p class="text-xs text-fuchsia-700">
                                <span class="font-semibold">Change Rate:</span>
                                {{ round(($dns['stats']['record_changes_24h'] / 24), 1) }} changes per hour
                            </p>
                        </div>
                    </div>
                </div>

                <!-- DNS Records Table -->
                <div class="overflow-x-auto bg-white/80 backdrop-blur-sm border-2 border-indigo-100 rounded-2xl shadow-2xl">
                    <div class="p-4 border-b border-indigo-100 bg-gradient-to-r from-indigo-50 to-violet-50">
                        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                            <div class="w-full sm:w-64">
                                <label for="search" class="sr-only">Search records</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input wire:model.live="search" type="search" id="search" class="block w-full pl-10 pr-3 py-2 border border-indigo-200 rounded-xl leading-5 bg-white/50 backdrop-blur-sm placeholder-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm" placeholder="Search records...">
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <select wire:model.live="recordType" class="block w-full pl-3 pr-10 py-2 text-base border-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-xl bg-white/50 backdrop-blur-sm">
                                    <option value="">All Types</option>
                                    @foreach(array_keys($dns['stats']['types_found']) as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                <button wire:click="sort" class="inline-flex items-center px-4 py-2 border border-indigo-200 shadow-lg text-sm leading-4 font-medium rounded-xl text-indigo-700 bg-white/50 backdrop-blur-sm hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
                                    <svg class="h-4 w-4 {{ $sortDesc ? 'rotate-180' : '' }} transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <table class="min-w-full divide-y divide-indigo-100">
                        <thead class="bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50">
                            <tr>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-extrabold text-indigo-700 uppercase tracking-wider whitespace-nowrap">Type</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-extrabold text-purple-700 uppercase tracking-wider whitespace-nowrap">Host</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-extrabold text-fuchsia-700 uppercase tracking-wider whitespace-nowrap">Target</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-extrabold text-pink-700 uppercase tracking-wider whitespace-nowrap">TTL</th>
                                <th scope="col" class="px-4 py-4 text-left text-xs font-extrabold text-rose-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-indigo-100 bg-white/50 backdrop-blur-sm">
                            @foreach($dnsRecords as $record)
                                <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-colors duration-200">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">{{ $record['type'] }}</span>
                                            <span class="text-xs text-indigo-500">({{ $record['class'] }})</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="relative group/host">
                                            <span class="text-xs font-medium text-gray-700 break-all truncate max-w-[200px] block group-hover/host:hidden">{{ $record['host'] }}</span>
                                            <span class="text-sm font-medium text-indigo-700 break-all hidden group-hover/host:block">{{ $record['host'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if($record['type'] === 'TXT')
                                            <div class="text-sm font-medium text-gray-700 break-all">
                                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                                    @if(isset($record['spf_strength']))
                                                        <span class="px-2 py-1 bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 rounded-md text-xs">SPF Strength: {{ $record['spf_strength'] }}</span>
                                                    @endif
                                                    @if(isset($record['dmarc_policy']))
                                                        <span class="px-2 py-1 bg-gradient-to-r from-purple-100 to-fuchsia-100 text-purple-800 rounded-md text-xs">DMARC: {{ $record['dmarc_policy'] }}</span>
                                                    @endif
                                                    @if(isset($record['bimi_record']))
                                                        <span class="px-2 py-1 bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 rounded-md text-xs">BIMI: Present</span>
                                                    @endif
                                                    @if(isset($record['google_site_verification']))
                                                        <span class="px-2 py-1 bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 rounded-md text-xs">Google Verification</span>
                                                    @endif
                                                </div>
                                                {{ $record['target'] }}
                                            </div>
                                        @else
                                            <span class="text-sm font-medium text-gray-700 break-all">{{ $record['target'] }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-indigo-500">TTL: {{ $record['ttl'] }} ({{ $record['ttl_human'] }})</span>
                                            <span class="text-xs text-purple-500">Expires: {{ \Carbon\Carbon::parse($record['expiry'])->diffForHumans() }}</span>
                                            @if(isset($record['last_change']))
                                                <span class="text-xs text-fuchsia-500">Last Change: {{ \Carbon\Carbon::parse($record['last_change'])->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record['is_valid'] ? 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800' : 'bg-gradient-to-r from-red-100 to-rose-100 text-red-800' }}">
                                            {{ $record['is_valid'] ? 'Valid' : 'Invalid' }}
                                        </span>
                                        @if(!$record['is_valid'])
                                            <div class="mt-2 text-xs text-gray-600">
                                                <p class="font-semibold">How to fix:</p>
                                                @if($record['type'] === 'A' || $record['type'] === 'AAAA')
                                                    <p>- Verify IP address is correct</p>
                                                    <p>- Check DNS propagation</p>
                                                @elseif($record['type'] === 'MX')
                                                    <p>- Verify mail server hostname</p>
                                                    <p>- Check priority value</p>
                                                @elseif($record['type'] === 'CNAME')
                                                    <p>- Verify target domain exists</p>
                                                    <p>- Check for CNAME conflicts</p>
                                                @elseif($record['type'] === 'TXT')
                                                    <p>- Check record format</p>
                                                    <p>- Verify SPF/DMARC syntax</p>
                                                @else
                                                    <p>- Review record configuration</p>
                                                    <p>- Contact DNS provider</p>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @if(count($record['issues']) > 0)
                                    <tr class="bg-gradient-to-r from-gray-50 to-indigo-50">
                                        <td colspan="5" class="px-8 py-4">
                                            <div class="space-y-2">
                                                @foreach($record['issues'] as $issue)
                                                    <div class="flex items-start gap-2">
                                                        <div class="mt-1">
                                                            @if(str_contains(strtolower($issue), 'critical') || str_contains(strtolower($issue), 'severe') || str_contains(strtolower($issue), 'urgent'))
                                                                <span class="flex h-2 w-2 rounded-full bg-gradient-to-r from-red-500 to-rose-500 animate-pulse"></span>
                                                            @elseif(str_contains(strtolower($issue), 'warning') || str_contains(strtolower($issue), 'recommend'))
                                                                <span class="flex h-2 w-2 rounded-full bg-gradient-to-r from-amber-500 to-yellow-500 animate-pulse"></span>
                                                            @else
                                                                <span class="flex h-2 w-2 rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 animate-pulse"></span>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <p class="text-sm text-gray-600">{{ $issue }}</p>
                                                            <div class="mt-1 text-xs text-gray-500">
                                                                <p class="font-semibold">Resolution steps:</p>
                                                                @if(str_contains(strtolower($issue), 'dnssec'))
                                                                    <p>- Enable DNSSEC with your DNS provider</p>
                                                                    <p>- Configure DS records with registrar</p>
                                                                @elseif(str_contains(strtolower($issue), 'spf'))
                                                                    <p>- Add SPF record with authorized IPs/domains</p>
                                                                    <p>- Validate SPF syntax</p>
                                                                @elseif(str_contains(strtolower($issue), 'dmarc'))
                                                                    <p>- Add DMARC policy record</p>
                                                                    <p>- Configure reporting addresses</p>
                                                                @elseif(str_contains(strtolower($issue), 'ttl'))
                                                                    <p>- Adjust TTL values based on update frequency</p>
                                                                    <p>- Consider lower TTLs for frequently changed records</p>
                                                                @else
                                                                    <p>- Review DNS configuration</p>
                                                                    <p>- Consult DNS provider documentation</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:w-1/4 space-y-8">
        <!-- DNSSEC Status Card -->
        <div class="bg-gradient-to-br from-{{ $dns['stats']['dnssec_status']['enabled'] ? 'emerald' : 'rose' }}-200 via-{{ $dns['stats']['dnssec_status']['enabled'] ? 'teal' : 'pink' }}-100 to-white border-[3px] border-{{ $dns['stats']['dnssec_status']['enabled'] ? 'emerald' : 'rose' }}-300 rounded-3xl p-10 shadow-2xl hover:shadow-{{ $dns['stats']['dnssec_status']['enabled'] ? 'emerald' : 'rose' }}-200 hover:-translate-y-1 transition-all duration-300">
            <h3 class="text-2xl font-black text-{{ $dns['stats']['dnssec_status']['enabled'] ? 'emerald-600' : 'rose-600' }} mb-8 flex items-center gap-4">
                <svg class="w-8 h-8 {{ $dns['stats']['dnssec_status']['enabled'] ? 'text-emerald-600' : 'text-rose-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                DNSSEC Status
            </h3>
            <div class="flex items-center gap-5 mb-8">
                <div class="w-6 h-6 rounded-full bg-gradient-to-r from-{{ $dns['stats']['dnssec_status']['enabled'] ? 'green-500' : 'red-500' }} to-{{ $dns['stats']['dnssec_status']['enabled'] ? 'green-600' : 'red-600' }} animate-pulse shadow-xl"></div>
                <span class="{{ $dns['stats']['dnssec_status']['enabled'] ? 'text-emerald-700' : 'text-rose-700' }} font-extrabold text-xl">
                    {{ $dns['stats']['dnssec_status']['enabled'] ? 'Enabled' : 'Disabled' }}
                </span>
            </div>
            <p class="text-base text-gray-700 bg-white/90 backdrop-blur-sm rounded-2xl p-6 border-2 border-gray-200 shadow-lg">
                {{ $dns['stats']['dnssec_status']['validation_status'] }}
            </p>
            @if(!$dns['stats']['dnssec_status']['enabled'])
                <div class="mt-6 pt-6 border-t-2 border-rose-300">
                    <div class="text-sm text-rose-700 space-y-3">
                        <p class="font-bold">How to enable DNSSEC:</p>
                        <ol class="list-decimal pl-5 space-y-2">
                            <li>Contact your DNS provider</li>
                            <li>Enable DNSSEC in provider settings</li>
                            <li>Get DS record details</li>
                            <li>Add DS record at registrar</li>
                            <li>Wait for propagation (24-48h)</li>
                        </ol>
                    </div>
                </div>
            @else
                <div class="mt-6 pt-6 border-t-2 border-emerald-300">
                    <div class="text-sm text-emerald-700 space-y-3">
                        <p><span class="font-bold">Algorithm:</span> {{ $dns['stats']['dnssec_status']['algorithm'] ?? 'RSA/SHA-256' }}</p>
                        <p><span class="font-bold">Key Type:</span> {{ $dns['stats']['dnssec_status']['key_type'] ?? 'ZSK' }}</p>
                        <p><span class="font-bold">Last Signed:</span> {{ isset($dns['stats']['dnssec_status']['last_signed']) ? \Carbon\Carbon::parse($dns['stats']['dnssec_status']['last_signed'])->diffForHumans() : 'Unknown' }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Record Types Card -->
        <div class="bg-gradient-to-br from-indigo-100 via-indigo-50 to-white border-2 border-indigo-200 rounded-2xl p-8 shadow-xl hover:shadow-indigo-100 transition-all duration-300">
            <h3 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center gap-3">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Record Types
            </h3>
            <div class="space-y-4">
                @foreach($dns['stats']['types_found'] as $type => $count)
                    <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow duration-200">
                        <div>
                            <span class="text-sm font-bold text-indigo-700">{{ $type }}</span>
                            <p class="text-xs text-gray-500 mt-1">{{ $this->getRecordTypeDescription($type) }}</p>
                        </div>
                        <div class="flex flex-col items-end">
                            <span class="text-sm font-extrabold text-gray-900 bg-indigo-100 px-4 py-1.5 rounded-lg shadow-inner">{{ $count }}</span>
                            <span class="text-xs text-gray-500 mt-1">{{ round(($count / $dns['stats']['total_records'] * 100), 1) }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Issues & Recommendations -->
        @if(count($dns['global_issues']) > 0 || count($dns['recommendations']) > 0)
            <div class="bg-gradient-to-br from-gray-100 via-gray-50 to-white border-2 border-gray-200 rounded-2xl p-8 shadow-xl hover:shadow-gray-100 transition-all duration-300">
                @if(count($dns['global_issues']) > 0)
                    <div class="mb-8">
                        <h3 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center gap-3">
                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Issues
                            <span class="text-xs bg-rose-100 text-rose-800 px-2 py-1 rounded-full">{{ count($dns['global_issues']) }}</span>
                        </h3>
                        <ul class="space-y-4">
                            @foreach($dns['global_issues'] as $issue)
                                <li class="flex items-start gap-4 bg-rose-50 p-5 rounded-xl border-2 border-rose-200 shadow-sm hover:border-rose-300 transition-colors">
                                    <svg class="w-6 h-6 text-rose-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <span class="text-sm font-medium text-rose-800">{{ $issue }}</span>
                                        <p class="text-xs text-rose-600 mt-1">Impact: {{ $this->getIssueImpact($issue) }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(count($dns['recommendations']) > 0)
                    <div>
                        <h3 class="text-xl font-extrabold text-gray-900 mb-6 flex items-center gap-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            Recommendations
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ count($dns['recommendations']) }}</span>
                        </h3>
                        <ul class="space-y-4">
                            @foreach($dns['recommendations'] as $recommendation)
                                <li class="flex items-start gap-4 bg-blue-50 p-5 rounded-xl border-2 border-blue-200 shadow-sm hover:border-blue-300 transition-colors">
                                    <svg class="w-6 h-6 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div>
                                        <span class="text-sm font-medium text-blue-800">{{ $recommendation }}</span>
                                        <p class="text-xs text-blue-600 mt-1">Priority: {{ $this->getRecommendationPriority($recommendation) }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
