<div class="bg-gradient-to-tr from-violet-100 via-indigo-50 to-blue-100 dark:from-gray-900 dark:via-indigo-950 dark:to-blue-950 shadow-2xl rounded-3xl p-10 border-2 border-indigo-200 dark:border-indigo-800">
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
        <!-- Domain Overview -->
        <div class="xl:col-span-4 bg-white/70 dark:bg-gray-800/70 rounded-2xl p-8 backdrop-blur-lg border border-indigo-200 dark:border-indigo-800">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="p-4 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl shadow-lg shadow-indigo-500/30">
                        <x-icon name="heroicon-o-globe-alt" class="w-8 h-8 text-white"/>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $registry['domain'] ?? 'N/A' }}</h2>
                        <p class="text-indigo-600 dark:text-indigo-400">Managed by {{ $registry['registrar'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="text-center px-6 py-3 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/50 dark:to-teal-900/50 rounded-xl border border-emerald-200 dark:border-emerald-800">
                        <p class="text-sm text-emerald-600 dark:text-emerald-400">Created</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $registry['created_date'] ?? 'N/A' }}</p>
                    </div>
                    <div class="text-center px-6 py-3 bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/50 dark:to-orange-900/50 rounded-xl border border-amber-200 dark:border-amber-800">
                        <p class="text-sm text-amber-600 dark:text-amber-400">Expires</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $registry['expiry_date'] ?? 'N/A' }}</p>
                    </div>
                    <div class="text-center px-6 py-3 bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/50 dark:to-indigo-900/50 rounded-xl border border-blue-200 dark:border-blue-800">
                        <p class="text-sm text-blue-600 dark:text-blue-400">Updated</p>
                        <p class="font-semibold text-gray-900 dark:text-white">{{ $registry['updated_date'] ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registrant Information -->
        <div class="xl:col-span-2 bg-white/70 dark:bg-gray-800/70 rounded-2xl p-8 backdrop-blur-lg border border-indigo-200 dark:border-indigo-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg shadow-purple-500/20">
                    <x-icon name="heroicon-o-user" class="w-6 h-6 text-white"/>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Contact Information</h3>
            </div>
            <div x-data="{ activeTab: 'registrant' }" class="space-y-6">
                <!-- Tabs -->
                <div class="flex space-x-1 bg-gray-100 dark:bg-gray-900/50 p-1 rounded-xl">
                    <button 
                        @click="activeTab = 'registrant'" 
                        :class="{ 'bg-white dark:bg-gray-800 shadow-sm': activeTab === 'registrant' }"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150"
                    >
                        Registrant
                    </button>
                    <button 
                        @click="activeTab = 'admin'" 
                        :class="{ 'bg-white dark:bg-gray-800 shadow-sm': activeTab === 'admin' }"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150"
                    >
                        Admin
                    </button>
                    <button 
                        @click="activeTab = 'technical'" 
                        :class="{ 'bg-white dark:bg-gray-800 shadow-sm': activeTab === 'technical' }"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150"
                    >
                        Technical
                    </button>
                    <button 
                        @click="activeTab = 'billing'" 
                        :class="{ 'bg-white dark:bg-gray-800 shadow-sm': activeTab === 'billing' }"
                        class="flex-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors duration-150"
                    >
                        Billing
                    </button>
                </div>

                <!-- Tab Panels -->
                <div class="mt-4">
                    <!-- Registrant Panel -->
                    <div x-show="activeTab === 'registrant'" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registrant']['name'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Organization</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registrant']['organization'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registrant']['email'] ?? 'N/A' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Phone</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registrant']['phone'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Fax</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registrant']['fax'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Address</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registrant']['address'] ?? 'N/A' }}</p>
                        </div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">City</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registrant']['city'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">State</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registrant']['state'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Country</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registrant']['country'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Panel -->
                    <div x-show="activeTab === 'admin'" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['admin_contact']['name'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Organization</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['admin_contact']['organization'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['admin_contact']['email'] ?? 'N/A' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Phone</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['admin_contact']['phone'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Fax</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['admin_contact']['fax'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Address</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['admin_contact']['address'] ?? 'N/A' }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Country</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['admin_contact']['country'] ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Technical Panel -->
                    <div x-show="activeTab === 'technical'" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['technical_contact']['name'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Organization</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['technical_contact']['organization'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['technical_contact']['email'] ?? 'N/A' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Phone</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['technical_contact']['phone'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Fax</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['technical_contact']['fax'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Address</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['technical_contact']['address'] ?? 'N/A' }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Country</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['technical_contact']['country'] ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Billing Panel -->
                    <div x-show="activeTab === 'billing'" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Name</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['billing_contact']['name'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Organization</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['billing_contact']['organization'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Email</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['billing_contact']['email'] ?? 'N/A' }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Phone</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['billing_contact']['phone'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Fax</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['billing_contact']['fax'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Address</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['billing_contact']['address'] ?? 'N/A' }}</p>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Country</p>
                            <p class="text-gray-900 dark:text-white font-medium">{{ $registry['billing_contact']['country'] ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Domain Status & Registry Data -->
        <div class="xl:col-span-2 bg-white/70 dark:bg-gray-800/70 rounded-2xl p-8 backdrop-blur-lg border border-indigo-200 dark:border-indigo-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-xl shadow-lg shadow-blue-500/20">
                    <x-icon name="heroicon-o-information-circle" class="w-6 h-6 text-white"/>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Domain Information</h3>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Premium Domain</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['premium'] ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Price Category</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['price_category'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">DNSSEC</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['dnssec'] ? 'Enabled' : 'Disabled' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Privacy</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['privacy_enabled'] ? 'Enabled' : 'Disabled' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Domain Age</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['domain_age'] ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Days Until Expiry</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['days_until_expiry'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Auto Renew</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['auto_renew'] ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Transfer Prohibited</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['transfer_prohibited'] ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Registrar IANA ID</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['registrar_iana_id'] ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Registrar URL</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['registrar_url'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Created Date</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['registry_created_date'] ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Expiry Date</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['registry_expiry_date'] ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Last Updated</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['registry_updated_date'] ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Last Transferred</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $registry['registry_data']['last_transferred_date'] ?? 'N/A' }}</p>
                    </div>
                </div>
                @if(isset($registry['registry_data']['registry_status_codes']) && count($registry['registry_data']['registry_status_codes']) > 0)
                    <div class="grid gap-3">
                        @foreach($registry['registry_data']['registry_status_codes'] as $status)
                            <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl">
                                <x-icon name="heroicon-o-check-circle" class="w-5 h-5 text-blue-500"/>
                                <span class="text-gray-700 dark:text-gray-300">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Nameservers -->
        <div class="xl:col-span-4 bg-white/70 dark:bg-gray-800/70 rounded-2xl p-8 backdrop-blur-lg border border-indigo-200 dark:border-indigo-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg shadow-green-500/20">
                    <x-icon name="heroicon-o-server" class="w-6 h-6 text-white"/>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Nameservers</h3>
            </div>
            @if(isset($registry['nameservers']) && count($registry['nameservers']) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($registry['nameservers'] as $nameserver)
                        <div class="flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl">
                            <x-icon name="heroicon-o-server" class="w-5 h-5 text-emerald-500"/>
                            <span class="text-gray-700 dark:text-gray-300">{{ $nameserver }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl text-gray-500 dark:text-gray-400">
                    No nameservers found
                </div>
            @endif
        </div>

        <!-- Additional Data -->
        <div class="xl:col-span-4 bg-white/70 dark:bg-gray-800/70 rounded-2xl p-8 backdrop-blur-lg border border-indigo-200 dark:border-indigo-800">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-3 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl shadow-lg shadow-purple-500/20">
                    <x-icon name="heroicon-o-document-text" class="w-6 h-6 text-white"/>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Additional Information</h3>
            </div>

            <div class="space-y-6">
                <!-- Domain Status Description -->
                @if(isset($registry['additional_data']['domain_status_description']))
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Domain Status</p>
                        <p class="text-gray-900 dark:text-white font-medium">{{ is_array($registry['additional_data']['domain_status_description']) ? implode(', ', $registry['additional_data']['domain_status_description']) : $registry['additional_data']['domain_status_description'] }}</p>
                    </div>
                @endif

                <!-- Domain Analytics -->
                @if(isset($registry['additional_data']['domain_analytics']))
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Analytics</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Traffic Rank</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['additional_data']['domain_analytics']['traffic_rank'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Traffic Stats</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['additional_data']['domain_analytics']['traffic_stats'] ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if(isset($registry['additional_data']['domain_analytics']['backlinks']) && count($registry['additional_data']['domain_analytics']['backlinks']) > 0)
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Backlinks</p>
                                <div class="space-y-2">
                                    @foreach($registry['additional_data']['domain_analytics']['backlinks'] as $backlink)
                                        <div class="text-gray-700 dark:text-gray-300">{{ $backlink }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(isset($registry['additional_data']['domain_analytics']['keywords']) && count($registry['additional_data']['domain_analytics']['keywords']) > 0)
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Keywords</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($registry['additional_data']['domain_analytics']['keywords'] as $keyword)
                                        <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded-full text-sm">{{ $keyword }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Security Information -->
                @if(isset($registry['additional_data']['security_info']))
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Security Status</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Malware Status</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['additional_data']['security_info']['malware_status'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Phishing Status</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['additional_data']['security_info']['phishing_status'] ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if(isset($registry['additional_data']['security_info']['blacklist_status']) && count($registry['additional_data']['security_info']['blacklist_status']) > 0)
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Blacklist Status</p>
                                <div class="space-y-2">
                                    @foreach($registry['additional_data']['security_info']['blacklist_status'] as $status)
                                        <div class="text-gray-700 dark:text-gray-300">{{ $status }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(isset($registry['additional_data']['security_info']['ssl_vulnerabilities']) && count($registry['additional_data']['security_info']['ssl_vulnerabilities']) > 0)
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">SSL Vulnerabilities</p>
                                <div class="space-y-2">
                                    @foreach($registry['additional_data']['security_info']['ssl_vulnerabilities'] as $vulnerability)
                                        <div class="text-gray-700 dark:text-gray-300">{{ $vulnerability }}</div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Hosting Information -->
                @if(isset($registry['additional_data']['hosting_info']))
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Hosting Details</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Hosting Provider</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['additional_data']['hosting_info']['hosting_provider'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">IP Address</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['additional_data']['hosting_info']['ip_address'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Server Type</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['additional_data']['hosting_info']['server_type'] ?? 'N/A' }}</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">IP Geolocation</p>
                                <p class="text-gray-900 dark:text-white font-medium">{{ $registry['additional_data']['hosting_info']['ip_geolocation'] ?? 'N/A' }}</p>
                            </div>
                        </div>

                        @if(isset($registry['additional_data']['hosting_info']['technologies']) && count($registry['additional_data']['hosting_info']['technologies']) > 0)
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Technologies</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($registry['additional_data']['hosting_info']['technologies'] as $tech)
                                        <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 rounded-full text-sm">{{ $tech }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
