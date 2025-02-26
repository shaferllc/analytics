<x-site :site="$site" :title="__('Tracking Code')" :description="__('Add analytics tracking to your website')" icon="heroicon-o-code-bracket">

    <x-loading />
    <x-breadcrumbs :breadcrumbs="[
        [
            'url' => route('dashboard'),
            'label' => __('Dashboard'),
            'icon' => 'heroicon-o-home',
        ],
        [
            'url' => route('sites.show', ['site' => $site->id]),
            'label' => $site->name,
            'icon' => 'heroicon-o-building-office-2',
        ],
        [
            'url' => route('sites.analytics.tracking-code', ['site' => $site->id]),
            'label' => __('Tracking Code'),
            'icon' => 'heroicon-o-code-bracket',
        ]
    ]" />

    <div class="w-full max-w-7xl mx-auto mt-4">
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
                    <div class="space-y-2 flex-initial">
                        <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                            <x-icon name="heroicon-o-code-bracket" class="w-6 h-6 text-emerald-500" />
                            <span>{{ __('Tracking Code') }}</span>
                        </h2>
                        <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 bg-slate-100/50 dark:bg-slate-700/20 px-3 py-1.5 rounded-lg">
                            <x-icon name="heroicon-o-link" class="w-4 h-4" />
                            <span class="font-mono break-all">{{ $site->domain }}</span>
                        </div>
                    </div>
                </div>

                <!-- Tracking Code Content -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                        <x-icon name="heroicon-o-information-circle" class="w-5 h-5 text-slate-400" />
                        <p>{!! __('Add this tracking code to your website:', ['head' => '<code class="bg-slate-100/50 dark:bg-slate-700/50 px-1.5 py-0.5 rounded text-emerald-500 font-mono">&lt;head&gt;</code>', 'body' => '<code class="bg-slate-100/50 dark:bg-slate-700/50 px-1.5 py-0.5 rounded text-emerald-500 font-mono">&lt;body&gt;</code>']) !!}</p>
                    </div>

                    @php
                    $trackingCode = "
TSMonitorConfig = {
    host: '".app('url')->to('/')."',
    id: '".$site->id."',
    events: [
        'scroll',
        'beforeunload',
        'click',
        'load',
        'mousemove',
        'keydown',
        'submit',
        'focus',
        'blur',
        'resize',
        'error',
        'contextmenu',
        'touchstart',
        'touchend',
        'touchmove'
    ],
    excludedElements: ['[data-no-track]', '.no-track'],
};
<script src='".app('url')->to('/')."/api/v1/monitor' async defer></script>
";
                    @endphp
                    <div class="relative">
                        <textarea
                            name="tracking_code"
                            class="w-full p-4 pr-48 bg-slate-50/50 dark:bg-slate-700/20 border border-slate-200/60 dark:border-slate-700/60 rounded-lg text-emerald-500 font-mono text-sm focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all duration-300"
                            id="i-tracking-code"
                            rows="24"
                            onclick="this.select();"
                            readonly
                            spellcheck="false">{{ $trackingCode }}</textarea>

                        <div class="absolute top-3 right-3">
                            <button
                                wire:click="copyToClipboard"
                                class="inline-flex items-center px-4 py-2 bg-emerald-500 hover:bg-emerald-400 text-white text-sm font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 hover:shadow-lg hover:shadow-emerald-500/25"
                            >
                                <x-icon name="heroicon-o-clipboard" class="w-4 h-4 mr-2" wire:loading.remove wire:target="copyToClipboard" />
                                <x-icon name="heroicon-o-check" class="w-4 h-4 mr-2" wire:loading.remove wire:target="copyToClipboard" x-show="$wire.copied" />
                                <span wire:loading.remove wire:target="copyToClipboard" x-text="$wire.copied ? '{{ __('Copied!') }}' : '{{ __('Copy') }}'"></span>
                                <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 mr-2 animate-spin" wire:loading wire:target="copyToClipboard" />
                            </button>
                        </div>
                    </div>

                    <!-- Proper Embedding Instructions -->
                    <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg mt-4">
                        <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Proper Embedding Instructions') }}</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
                            {{ __('For optimal tracking, ensure the script is placed:') }}
                        </p>
                        <ul class="text-sm text-slate-600 dark:text-slate-300 space-y-2 list-disc pl-5">
                            <li>{{ __('As the last element in the <head> section') }}</li>
                            <li>{{ __('Or just before the closing </body> tag') }}</li>
                            <li>{{ __('Do not place it inside other scripts or conditional blocks') }}</li>
                            <li>{{ __('Ensure the script loads asynchronously (async defer attributes are already included)') }}</li>
                            <li>{{ __('Avoid modifying the script attributes or content') }}</li>
                            <li>{{ __('For single-page applications (SPAs), ensure the script is loaded on initial page load') }}</li>
                        </ul>
                    </div>

                    <!-- Installation Instructions -->
                    <div x-cloak x-data="{ open: false }" class="pt-4">
                        <button @click="open = !open" class="flex items-center text-sm text-emerald-500 hover:text-emerald-600 cursor-pointer">
                            <template x-if="open">
                                <x-icon name="heroicon-o-chevron-up" class="w-4 h-4 transition-transform" />
                            </template>
                            <template x-if="!open">
                                <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 transition-transform" />
                            </template>
                            <span class="ml-2">{{ __('Show Installation Instructions') }}</span>
                        </button>

                        <div x-show="open" x-collapse class="mt-4 space-y-4">
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('WordPress') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Add the tracking code to your theme\'s footer.php file, just before the closing </body> tag.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Shopify') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Online Store > Themes > Actions > Edit code, and add the tracking code to the theme.liquid file, just before the closing </body> tag.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Wix') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Settings > Tracking & Analytics > Add Custom Code, and paste the tracking code in the "Body - End" section.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Squarespace') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Settings > Advanced > Code Injection, and paste the tracking code in the Footer section.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Joomla') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Extensions > Templates > Templates, edit your template and paste the tracking code just before the closing </body> tag.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Drupal') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Structure > Blocks > Add Block, create a new block with the tracking code and set it to display in the Footer region.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Magento') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Content > Configuration > Edit your theme, and paste the tracking code in the Footer > Miscellaneous HTML field.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Webflow') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Project Settings > Custom Code, and paste the tracking code in the "Before </body> tag" section.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Ghost') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Settings > Code Injection, and paste the tracking code in the Site Footer section.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('PrestaShop') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Modules > Positions, search for "displayFooter" and add a new module with the tracking code.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('OpenCart') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Go to Extensions > Modifications > Add New, create a new modification to insert the tracking code before the closing </body> tag.') }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 dark:bg-slate-700/20 rounded-lg">
                                <h3 class="font-semibold text-slate-900 dark:text-slate-100 mb-2">{{ __('Custom HTML') }}</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('Paste the tracking code just before the closing </body> tag in your HTML files.') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Status -->
                    @if($verificationStatus)
                        <div class="flex items-center gap-2 mt-4 text-sm {{ $verificationStatus === 'verified' ? 'text-emerald-500' : 'text-rose-500' }}">
                            <x-icon name="heroicon-o-check-circle" class="w-5 h-5" />
                            <span>{{ $verificationStatus === 'verified' ? __('Tracking code is properly installed') : __('Tracking code not detected') }}</span>
                        </div>
                    @endif

                    <!-- Configuration Options -->
                    <div class="p-6 bg-white/50 dark:bg-slate-800/20 backdrop-blur-sm rounded-xl shadow-sm border border-slate-200/50 dark:border-slate-700/20 mt-6">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                    {{ __('Configuration Options') }}
                                </h3>
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    {{ __('Customize the tracking behavior using these configuration options:') }}
                                </p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Basic Options -->
                                <div class="p-4 bg-slate-50/50 dark:bg-slate-700/10 rounded-lg">
                                    <h4 class="font-medium text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-2">
                                        <x-icon name="heroicon-o-cog" class="w-5 h-5 text-slate-400" />
                                        Basic Options
                                    </h4>
                                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">host</code>
                                            <span>Your analytics server URL</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">id</code>
                                            <span>Unique site identifier</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">debug</code>
                                            <span>Enable console debugging</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">browserDebug</code>
                                            <span>Show debug info in browser</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Tracking Options -->
                                <div class="p-4 bg-slate-50/50 dark:bg-slate-700/10 rounded-lg">
                                    <h4 class="font-medium text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-2">
                                        <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 text-slate-400" />
                                        Tracking Options
                                    </h4>
                                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">trackingEnabled</code>
                                            <span>Enable/disable tracking</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">excludedElements</code>
                                            <span>CSS selectors to exclude</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">samplingRate</code>
                                            <span>Percentage of events to track</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">privacyMode</code>
                                            <span>Enable privacy restrictions</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Session Options -->
                                <div class="p-4 bg-slate-50/50 dark:bg-slate-700/10 rounded-lg">
                                    <h4 class="font-medium text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-2">
                                        <x-icon name="heroicon-o-clock" class="w-5 h-5 text-slate-400" />
                                        Session Options
                                    </h4>
                                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">maxEventsPerSession</code>
                                            <span>Maximum events per session</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">sessionTimeout</code>
                                            <span>Session timeout in milliseconds</span>
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">csrfToken</code>
                                            <span>CSRF token for secure requests</span>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Event Options -->
                                <div class="p-4 bg-slate-50/50 dark:bg-slate-700/10 rounded-lg">
                                    <h4 class="font-medium text-slate-800 dark:text-slate-200 mb-3 flex items-center gap-2">
                                        <x-icon name="heroicon-o-bell" class="w-5 h-5 text-slate-400" />
                                        Event Options
                                    </h4>
                                    <ul class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                                        <li class="flex items-center gap-2">
                                            <code class="px-2 py-1 bg-slate-100 dark:bg-slate-700 rounded text-sm">events</code>
                                            <span>Array of events to track</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-site>
