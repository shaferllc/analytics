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
                    'icon' => 'heroicon-o-cpu-chip',
                ],
                [
                    'url' => route('websites.analytics.user-agents', ['website' => $site->id]),
                    'label' => __('User Agents'),
                    'icon' => 'heroicon-o-user',
                ]
            ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('User Agents')"
            :description="__('User agents are strings that identify the browser and operating system used to access your website.')"
            :totalPageviews="$total"
            :icon="'heroicon-o-user'"
            :totalText="__('Total User Agents')"
            :data="$data"
            :total="$total"
            :first="$first"
            :last="$last"
            :website="$site"
            :daterange="$daterange"
            :perPage="$perPage"
            :sortBy="$sortBy"
            :sort="$sort"
            :from="$from"
            :sortWords="['count' => __('Pageviews'), 'value' => __('User Agent')]"
            :to="$to"
            :search="$search"
        />

        <div>
            @if(count($data) == 0)
                <x-analytics::no-results />
            @else
                <div x-data="{ view: '{{$display}}', hoveredItem: null, animate: true }"
                     x-init="setTimeout(() => animate = false, 1000)"
                     class="space-y-4">
                    <x-analytics::view-switcher :data="$data" color="rose" />

                    <x-analytics::view view="list" color="rose" class="bg-gradient-to-br from-rose-900 to-rose-950 rounded-xl shadow-lg border border-rose-800 p-6 backdrop-blur-xl">
                        <div class="flex flex-col space-y-6">
                            @foreach($data as $userAgent)
                                <div class="flex items-center space-x-4 group hover:bg-rose-800/20 p-4 rounded-lg transition-all duration-300 hover:scale-[1.02] hover:shadow-lg border border-rose-800">
                                    <div class="flex-1">
                                        <div class="relative">
                                            <div class="text-sm text-rose-100 mb-2">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <x-tooltip :text="__('Browser: :browser :version', ['browser' => $userAgent->parsed['browser'], 'version' => $userAgent->parsed['browser_version']])" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-rose-200 to-rose-100 bg-clip-text text-transparent">
                                                                    <x-icon name="heroicon-o-globe-alt" class="w-4 h-4 inline mr-1" />
                                                                    {{ $userAgent->parsed['browser'] }} {{ $userAgent->parsed['browser_version'] }}
                                                                </span>
                                                            </x-tooltip>
                                                            <span class="text-rose-300">{{ __('on') }}</span>
                                                            <x-tooltip :text="__('Operating System: :os :version', ['os' => $userAgent->parsed['operating_system'], 'version' => $userAgent->parsed['os_version']])" class="group-hover:opacity-100">
                                                                <span class="font-medium bg-gradient-to-r from-rose-200 to-rose-100 bg-clip-text text-transparent">
                                                                    <x-icon name="heroicon-o-computer-desktop" class="w-4 h-4 inline mr-1" />
                                                                    {{ $userAgent->parsed['operating_system'] }}
                                                                </span>
                                                                @if($userAgent->parsed['os_version'])
                                                                    <span class="text-rose-300">{{ $userAgent->parsed['os_version'] }}</span>
                                                                @endif
                                                            </x-tooltip>
                                                        </div>
                                                        <div class="flex items-center space-x-2 mt-1">
                                                            <x-tooltip :text="__('Architecture: :arch', ['arch' => $userAgent->parsed['architecture']])" class="group-hover:opacity-100">
                                                                <span class="text-xs text-rose-400 hover:text-rose-300 transition-colors">
                                                                    <x-icon name="heroicon-o-cpu-chip" class="w-3 h-3 inline mr-1" />
                                                                    {{ $userAgent->parsed['architecture'] }}
                                                                </span>
                                                            </x-tooltip>
                                                            @if($userAgent->parsed['screen_resolution'] !== 'Unknown')
                                                                <x-tooltip :text="__('Screen Resolution: :res', ['res' => $userAgent->parsed['screen_resolution']])" class="group-hover:opacity-100">
                                                                    <span class="text-xs text-rose-400 hover:text-rose-300 transition-colors">
                                                                        <x-icon name="heroicon-o-rectangle-stack" class="w-3 h-3 inline mr-1" />
                                                                        {{ $userAgent->parsed['screen_resolution'] }}
                                                                    </span>
                                                                </x-tooltip>
                                                            @endif
                                                            @if($userAgent->parsed['language'])
                                                                <x-tooltip :text="__('Language: :lang', ['lang' => $userAgent->parsed['language']])" class="group-hover:opacity-100">
                                                                    <span class="text-xs text-rose-400 hover:text-rose-300 transition-colors">
                                                                        <x-icon name="heroicon-o-language" class="w-3 h-3 inline mr-1" />
                                                                        {{ $userAgent->parsed['language'] }}
                                                                    </span>
                                                                </x-tooltip>
                                                            @endif
                                                            @if($userAgent->parsed['webkit_version'])
                                                                <x-tooltip :text="__('WebKit Version: :version', ['version' => $userAgent->parsed['webkit_version']])" class="group-hover:opacity-100">
                                                                    <span class="text-xs text-rose-400 hover:text-rose-300 transition-colors">
                                                                        <x-icon name="heroicon-o-code-bracket" class="w-3 h-3 inline mr-1" />
                                                                        {{ __('WebKit') }} {{ $userAgent->parsed['webkit_version'] }}
                                                                    </span>
                                                                </x-tooltip>
                                                            @endif
                                                            @if($userAgent->parsed['device_model'])
                                                                <x-tooltip :text="__('Device Model: :model', ['model' => $userAgent->parsed['device_model']])" class="group-hover:opacity-100">
                                                                    <span class="text-xs text-rose-400 hover:text-rose-300 transition-colors">
                                                                        <x-icon name="heroicon-o-device-phone-mobile" class="w-3 h-3 inline mr-1" />
                                                                        {{ $userAgent->parsed['device_model'] }}
                                                                    </span>
                                                                </x-tooltip>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center space-x-2">
                                                        <x-tooltip :text="__('Device Type: :type', ['type' => $userAgent->parsed['device_type']])" class="group-hover:opacity-100">
                                                            <span class="px-2 py-0.5 text-xs rounded-full transition-colors duration-300 {{
                                                                match($userAgent->parsed['device_type']) {
                                                                    'Mobile' => 'bg-rose-500 hover:bg-rose-400',
                                                                    'Tablet' => 'bg-rose-600 hover:bg-rose-500',
                                                                    'Desktop' => 'bg-rose-700 hover:bg-rose-600',
                                                                    'TV' => 'bg-rose-800 hover:bg-rose-700',
                                                                    'Console' => 'bg-rose-900 hover:bg-rose-800',
                                                                    default => 'bg-rose-700 hover:bg-rose-600'
                                                                }
                                                            }}">
                                                                <x-icon name="heroicon-o-device-phone-mobile" class="w-3 h-3 inline mr-1" />
                                                                {{ __($userAgent->parsed['device_type']) }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('Browser Engine: :engine', ['engine' => $userAgent->parsed['engine']])" class="group-hover:opacity-100">
                                                            <span class="px-2 py-0.5 text-xs rounded-full bg-rose-700 hover:bg-rose-600 transition-colors duration-300">
                                                                <x-icon name="heroicon-o-cog" class="w-3 h-3 inline mr-1" />
                                                                {{ $userAgent->parsed['engine'] }}
                                                            </span>
                                                        </x-tooltip>
                                                        @if($userAgent->parsed['is_bot'])
                                                            <x-tooltip :text="__('This is a bot/crawler')" class="group-hover:opacity-100">
                                                                <span class="px-2 py-0.5 text-xs rounded-full bg-red-700 hover:bg-red-600 transition-colors duration-300">
                                                                    <x-icon name="heroicon-o-bug-ant" class="w-3 h-3 inline mr-1" />
                                                                    {{ __('Bot') }}
                                                                </span>
                                                            </x-tooltip>
                                                        @endif
                                                        @if($userAgent->parsed['is_social_app'])
                                                            <x-tooltip :text="__('Social Media App: :app', ['app' => $userAgent->parsed['social_app']])" class="group-hover:opacity-100">
                                                                <span class="px-2 py-0.5 text-xs rounded-full bg-blue-700 hover:bg-blue-600 transition-colors duration-300">
                                                                    <x-icon name="heroicon-o-share" class="w-3 h-3 inline mr-1" />
                                                                    {{ $userAgent->parsed['social_app'] }}
                                                                </span>
                                                            </x-tooltip>
                                                        @endif
                                                        @if($userAgent->parsed['supports_javascript'])
                                                            <x-tooltip :text="__('Supports JavaScript')" class="group-hover:opacity-100">
                                                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-700 hover:bg-green-600 transition-colors duration-300">
                                                                    <x-icon name="heroicon-o-code-bracket" class="w-3 h-3 inline mr-1" />
                                                                    {{ __('JS') }}
                                                                </span>
                                                            </x-tooltip>
                                                        @endif
                                                        @if($userAgent->parsed['supports_cookies'])
                                                            <x-tooltip :text="__('Supports Cookies')" class="group-hover:opacity-100">
                                                                <span class="px-2 py-0.5 text-xs rounded-full bg-green-700 hover:bg-green-600 transition-colors duration-300">
                                                                    <svg class="w-3 h-3 inline mr-1" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 11.34 21.94 10.7 21.82 10.08C21.0743 10.7375 20.1195 11.0889 19.13 11.06C18.1174 11.0262 17.1548 10.6064 16.4303 9.88191C15.7058 9.15741 15.286 8.19484 15.2522 7.18224C15.2233 6.19274 15.5747 5.23794 16.23 4.49C15.61 4.37 14.97 4.31 14.31 4.31C13.65 4.31 13 4.37 12.39 4.49C12.7243 5.47117 12.7633 6.52559 12.5024 7.52755C12.2415 8.52951 11.6919 9.43284 10.9207 10.1305C10.1495 10.8282 9.19244 11.2867 8.16998 11.4402C7.14751 11.5937 6.10093 11.4359 5.16 10.9867C4.42 11.5667 3.79 12.2667 3.32 13.0667C4.21013 13.5703 4.91593 14.3561 5.33311 15.3075C5.75029 16.2589 5.85752 17.3225 5.64 18.34C7.11 19.41 8.91 20 10.86 20C11.52 20 12.17 19.94 12.78 19.82C12.4457 18.8388 12.4067 17.7844 12.6676 16.7825C12.9285 15.7805 13.4781 14.8772 14.2493 14.1795C15.0205 13.4818 15.9776 13.0233 17 12.8698C18.0225 12.7163 19.0691 12.8741 20.01 13.3233C20.75 12.7433 21.38 12.0433 21.85 11.2433C20.9599 10.7397 20.2541 9.95392 19.8369 9.00252C19.4197 8.05112 19.3125 6.98752 19.53 5.97C18.06 4.9 16.26 4.31 14.31 4.31C13.65 4.31 13 4.37 12.39 4.49C12.7243 5.47117 12.7633 6.52559 12.5024 7.52755C12.2415 8.52951 11.6919 9.43284 10.9207 10.1305C10.1495 10.8282 9.19244 11.2867 8.16998 11.4402C7.14751 11.5937 6.10093 11.4359 5.16 10.9867C4.42 11.5667 3.79 12.2667 3.32 13.0667C4.21013 13.5703 4.91593 14.3561 5.33311 15.3075C5.75029 16.2589 5.85752 17.3225 5.64 18.34C7.11 19.41 8.91 20 10.86 20C16.38 20 20.86 15.52 20.86 10C20.86 9.34 20.8 8.7 20.68 8.08C19.9343 8.7375 18.9795 9.08893 17.99 9.06C16.9774 9.02624 16.0148 8.60641 15.2903 7.88191C14.5658 7.15741 14.146 6.19484 14.1122 5.18224C14.0833 4.19274 14.4347 3.23794 15.09 2.49C14.47 2.37 13.83 2.31 13.17 2.31C12.51 2.31 11.86 2.37 11.25 2.49" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                        <circle cx="7.5" cy="6.5" r="1" fill="currentColor"/>
                                                                        <circle cx="16.5" cy="16.5" r="1" fill="currentColor"/>
                                                                        <circle cx="10.5" cy="14.5" r="1" fill="currentColor"/>
                                                                    </svg>
                                                                    {{ __('Cookies') }}
                                                                </span>
                                                            </x-tooltip>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-xs text-rose-400 mt-1 break-all hover:text-rose-300 transition-colors">
                                                    {{ $userAgent->parsed['raw'] }}
                                                </div>
                                            </div>
                                            <div class="flex flex-col space-y-1">
                                                <div class="overflow-hidden h-2 text-xs flex rounded-lg bg-rose-700/30">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($userAgent->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-rose-400 to-rose-600 transition-all duration-300 hover:from-rose-300 hover:to-rose-500">
                                                    </div>
                                                </div>
                                                <div class="flex justify-between text-xs text-rose-300">
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('Total pageviews from this user agent')" class="group-hover:opacity-100">
                                                            <span class="hover:text-rose-200 transition-colors">
                                                                <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1" />
                                                                {{ number_format($userAgent->count, 0, __('.'), __(',')) }} {{ __('pageviews') }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('Unique sessions from this user agent')" class="group-hover:opacity-100">
                                                            <span class="hover:text-rose-200 transition-colors">
                                                                <x-icon name="heroicon-o-user-group" class="w-3 h-3 inline mr-1" />
                                                                {{ number_format($userAgent->unique_sessions, 0, __('.'), __(',')) }} {{ __('sessions') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                    <div class="flex space-x-4">
                                                        <x-tooltip :text="__('Percentage of total pageviews')" class="group-hover:opacity-100">
                                                            <span class="hover:text-rose-200 transition-colors">
                                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                                {{ $aggregates['total_count'] > 0 ? number_format(($userAgent->count / $aggregates['total_count']) * 100, 1) : 0 }}% {{ __('of pageviews') }}
                                                            </span>
                                                        </x-tooltip>
                                                        <x-tooltip :text="__('Percentage of total sessions')" class="group-hover:opacity-100">
                                                            <span class="hover:text-rose-200 transition-colors">
                                                                <x-icon name="heroicon-o-chart-bar" class="w-3 h-3 inline mr-1" />
                                                                {{ $aggregates['unique_sessions'] > 0 ? number_format(($userAgent->unique_sessions / $aggregates['unique_sessions']) * 100, 1) : 0 }}% {{ __('of sessions') }}
                                                            </span>
                                                        </x-tooltip>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-analytics::view>

                    <x-analytics::view view="cards" color="rose" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($data as $userAgent)
                            <div class="bg-gradient-to-br from-rose-900 to-rose-950 rounded-xl shadow-lg border border-rose-800 p-2 hover:scale-[1.02] transition-transform duration-200">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-rose-800/20 blur-xl rounded-full"></div>
                                            <div class="relative bg-gradient-to-br from-rose-700 to-rose-900 p-3 rounded-full">
                                                @if($userAgent->parsed['is_mobile'])
                                                    <x-tooltip :text="__('Mobile Device')">
                                                        <x-icon name="heroicon-o-device-phone-mobile" class="w-6 h-6 text-rose-200" />
                                                    </x-tooltip>
                                                @elseif($userAgent->parsed['is_tablet'])
                                                    <x-tooltip :text="__('Tablet Device')">
                                                        <x-icon name="heroicon-o-device-tablet" class="w-6 h-6 text-rose-200" />
                                                    </x-tooltip>
                                                @else
                                                    <x-tooltip :text="__('Desktop Device')">
                                                        <x-icon name="heroicon-o-computer-desktop" class="w-6 h-6 text-rose-200" />
                                                    </x-tooltip>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex flex-wrap gap-2 mb-2">
                                                <x-tooltip :text="__('Browser: :browser :version', ['browser' => $userAgent->parsed['browser'], 'version' => $userAgent->parsed['browser_version']])">
                                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-rose-700/50 text-rose-200 hover:bg-rose-600/50 transition-colors duration-200">
                                                        <x-icon name="heroicon-o-globe-alt" class="w-3 h-3 inline mr-1" />
                                                        {{ $userAgent->parsed['browser'] }} {{ $userAgent->parsed['browser_version'] }}
                                                    </span>
                                                </x-tooltip>
                                                <x-tooltip :text="__('Operating System: :os :version', ['os' => $userAgent->parsed['operating_system'], 'version' => $userAgent->parsed['os_version']])">
                                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-rose-700/50 text-rose-200 hover:bg-rose-600/50 transition-colors duration-200">
                                                        <x-icon name="heroicon-o-computer-desktop" class="w-3 h-3 inline mr-1" />
                                                        {{ $userAgent->parsed['operating_system'] }}
                                                    </span>
                                                </x-tooltip>
                                            </div>
                                            <div class="text-xs text-rose-400 break-all hover:text-rose-300 transition-colors duration-200">
                                                <x-tooltip :text="__('Raw User Agent String')">
                                                    <x-icon name="heroicon-o-code-bracket" class="w-3 h-3 inline mr-1" />
                                                    {{ $userAgent->parsed['raw'] }}
                                                </x-tooltip>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col space-y-3">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <x-tooltip :text="__('Total pageviews from this user agent')">
                                            <div class="bg-rose-800/20 rounded-lg p-3 hover:bg-rose-700/30 transition-colors duration-200">
                                                <span class="text-rose-300">
                                                    <x-icon name="heroicon-o-eye" class="w-3 h-3 inline mr-1" />
                                                    {{ __('Pageviews') }}
                                                </span>
                                                <span class="text-rose-100 font-semibold ml-2">{{ number_format($userAgent->count, 0, __('.'), __(',')) }}</span>
                                            </div>
                                        </x-tooltip>
                                        <x-tooltip :text="__('Unique sessions from this user agent')">
                                            <div class="bg-rose-800/20 rounded-lg p-3 hover:bg-rose-700/30 transition-colors duration-200">
                                                <span class="text-rose-300">
                                                    <x-icon name="heroicon-o-user-group" class="w-3 h-3 inline mr-1" />
                                                    {{ __('Sessions') }}
                                                </span>
                                                <span class="text-rose-100 font-semibold ml-2">{{ number_format($userAgent->unique_sessions, 0, __('.'), __(',')) }}</span>
                                            </div>
                                        </x-tooltip>
                                    </div>
                                    <div class="relative pt-1">
                                        <div class="overflow-hidden h-3 text-xs flex rounded-lg bg-rose-700/30">
                                            <div style="width: {{ $aggregates['total_count'] > 0 ? ($userAgent->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                class="shadow-none bg-gradient-to-r from-rose-400 to-rose-600 transition-all duration-500 hover:from-rose-500 hover:to-rose-700">
                                            </div>
                                        </div>
                                        <x-tooltip :text="__('Percentage of total pageviews')">
                                            <div class="text-xs text-rose-300 mt-2 text-right font-medium">
                                                <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                {{ number_format(($userAgent->count / $aggregates['total_count']) * 100, 1) }}% {{ __('of total') }}
                                            </div>
                                        </x-tooltip>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-analytics::view>

                    <x-analytics::view view="compact" color="rose" class="overflow-hidden rounded-xl border border-rose-800 shadow-lg shadow-rose-900/20">
                        <table class="min-w-full divide-y divide-rose-800">
                            <thead class="bg-gradient-to-br from-rose-900 to-rose-950">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-rose-300 uppercase tracking-wider cursor-pointer hover:text-rose-200 transition-colors duration-200" wire:click="$set('sort', '{{ $sort === 'asc' ? 'desc' : 'asc' }}'); $set('sortBy', 'value')">
                                        <div class="flex items-center">
                                            <x-tooltip :text="__('Sort by user agent string')">
                                                <span><x-icon name="heroicon-o-code-bracket" class="w-4 h-4 inline mr-1" />{{ __('User Agent') }}</span>
                                            </x-tooltip>
                                            @if($sortBy === 'value')
                                                @if($sort === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-4 h-4 ml-1 text-rose-200" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-4 h-4 ml-1 text-rose-200" />
                                                @endif
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-rose-300 uppercase tracking-wider cursor-pointer hover:text-rose-200 transition-colors duration-200" wire:click="$set('sort', '{{ $sort === 'asc' ? 'desc' : 'asc' }}'); $set('sortBy', 'count')">
                                        <div class="flex items-center justify-end">
                                            <x-tooltip :text="__('Sort by number of pageviews')">
                                                <div class="flex items-center">
                                                    <x-icon name="heroicon-o-eye" class="w-4 h-4 inline mr-1" />
                                                    {{ __('Pageviews') }}
                                                </div>
                                            </x-tooltip>
                                            @if($sortBy === 'count')
                                                @if($sort === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-4 h-4 ml-1 text-rose-200" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-4 h-4 ml-1 text-rose-200" />
                                                @endif
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-rose-300 uppercase tracking-wider cursor-pointer hover:text-rose-200 transition-colors duration-200" wire:click="$set('sort', '{{ $sort === 'asc' ? 'desc' : 'asc' }}'); $set('sortBy', 'unique_sessions')">
                                        <div class="flex items-center justify-end">
                                            <x-tooltip :text="__('Sort by number of unique sessions')">
                                                <div class="flex items-center">
                                                    <x-icon name="heroicon-o-user-group" class="w-4 h-4 inline mr-1" />
                                                    {{ __('Sessions') }}
                                                </div>
                                            </x-tooltip>
                                            @if($sortBy === 'unique_sessions')
                                                @if($sort === 'asc')
                                                    <x-heroicon-s-chevron-up class="w-4 h-4 ml-1 text-rose-200" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-4 h-4 ml-1 text-rose-200" />
                                                @endif
                                            @endif
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-rose-300 uppercase tracking-wider">
                                        <x-tooltip :text="__('Browser name and version')">
                                            <span><x-icon name="heroicon-o-globe-alt" class="w-4 h-4 inline mr-1" />{{ __('Browser') }}</span>
                                        </x-tooltip>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-rose-300 uppercase tracking-wider">
                                        <x-tooltip :text="__('Operating system')">
                                            <span><x-icon name="heroicon-o-computer-desktop" class="w-4 h-4 inline mr-1" />{{ __('OS') }}</span>
                                        </x-tooltip>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-rose-300 uppercase tracking-wider">
                                        <x-tooltip :text="__('Percentage of total pageviews')">
                                            <span><x-icon name="heroicon-o-chart-pie" class="w-4 h-4 inline mr-1" />{{ __('Usage') }}</span>
                                        </x-tooltip>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-gradient-to-br from-rose-950 to-rose-900/90 divide-y divide-rose-800/50">
                                @foreach($data as $userAgent)
                                    <tr class="hover:bg-rose-900/50 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="text-sm text-rose-100" x-data="{ showFull: false }" @mouseenter="showFull = true" @mouseleave="showFull = false">
                                                    <x-tooltip :text="__('Raw user agent string')">
                                                        <span x-show="showFull" class="break-all">{{ $userAgent->parsed['raw'] }}</span>
                                                        <span x-show="!showFull" x-cloak>{{ Str::limit($userAgent->parsed['raw'], 70) }}</span>
                                                    </x-tooltip>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-rose-100">
                                            <x-tooltip :text="__('Total pageviews from this user agent')">
                                                {{ number_format($userAgent->count, 0, __('.'), __(',')) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-rose-100">
                                            <x-tooltip :text="__('Unique sessions from this user agent')">
                                                {{ number_format($userAgent->unique_sessions, 0, __('.'), __(',')) }}
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <x-tooltip :text="__('Browser: :browser :version', ['browser' => $userAgent->parsed['browser'], 'version' => $userAgent->parsed['browser_version']])">
                                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-rose-700/60 to-rose-600/60 text-rose-100 shadow-sm">
                                                    <x-icon name="heroicon-o-globe-alt" class="w-3 h-3 inline mr-1" />
                                                    {{ $userAgent->parsed['browser'] }} {{ $userAgent->parsed['browser_version'] }}
                                                </span>
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <x-tooltip :text="__('Operating System: :os', ['os' => $userAgent->parsed['operating_system']])">
                                                <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gradient-to-r from-rose-700/60 to-rose-600/60 text-rose-100 shadow-sm">
                                                    <x-icon name="heroicon-o-computer-desktop" class="w-3 h-3 inline mr-1" />
                                                    {{ $userAgent->parsed['operating_system'] }}
                                                </span>
                                            </x-tooltip>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-tooltip :text="__('Percentage of total pageviews: :percent%', ['percent' => number_format(($userAgent->count / $aggregates['total_count']) * 100, 1)])">
                                                <div class="w-32 h-2.5 text-xs flex rounded-full bg-rose-800/30 ml-auto overflow-hidden">
                                                    <div style="width: {{ $aggregates['total_count'] > 0 ? ($userAgent->count / $aggregates['total_count']) * 100 : 0 }}%"
                                                        class="shadow-lg bg-gradient-to-r from-rose-500 to-rose-400 transition-all duration-300 hover:from-rose-400 hover:to-rose-300">
                                                    </div>
                                                </div>
                                                <div class="text-xs font-medium text-rose-300 mt-1.5 text-right">
                                                    <x-icon name="heroicon-o-chart-pie" class="w-3 h-3 inline mr-1" />
                                                    {{ number_format(($userAgent->count / $aggregates['total_count']) * 100, 1) }}%
                                                </div>
                                            </x-tooltip>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </x-analytics::view>

                </div>
                <div class="mt-6">
                    <x-analytics::pagination :data="$data" />
                </div>
            @endif
        </div>
    </div>
</x-website>
