<div x-cloak x-data="{ 
    hoverTab: null,
    activeTab: 'certificates',
    activeGroup: null,
    animate: true,
    init() {
        setTimeout(() => this.animate = false, 500);
        this.initBackgroundAnimation();
    },
    initBackgroundAnimation() {
        const nav = this.$el.querySelector('nav');
        let hue = 0;
        setInterval(() => {
            hue = (hue + 1) % 360;
            nav.style.background = `linear-gradient(45deg, 
                hsla(${hue}, 70%, 95%, 0.8),
                hsla(${(hue + 60) % 360}, 70%, 95%, 0.8),
                hsla(${(hue + 120) % 360}, 70%, 95%, 0.8)
            )`;
        }, 100);
    }
}">
    <nav class="z-50 relative flex justify-center flex-wrap gap-4 p-4 bg-gradient-to-br from-blue-100 via-indigo-100 to-purple-100 dark:from-blue-900/50 dark:via-indigo-900/50 dark:to-purple-900/50 rounded-2xl shadow-xl border border-blue-200/50 dark:border-blue-800/50 backdrop-blur-lg transition-all duration-100" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4" 
        x-transition:enter-end="opacity-100 translate-y-0"
        aria-label="Tabs">
        @php
        $menuGroups = [
            'Security & SSL' => [
                'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                'tooltip' => 'Security, SSL Certificates and HTTP Headers',
                'animation' => 'animate-pulse',
                'items' => [
                    'certificates' => [
                        'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
                        'label' => 'SSL',
                        'tooltip' => 'SSL Certificate Information & Status',
                        'animation' => 'animate-bounce'
                    ],
                    'security' => [
                        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                        'label' => 'Security',
                        'tooltip' => 'Security Analysis & Recommendations',
                        'animation' => 'animate-spin'
                    ],
                    'headers' => [
                        'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                        'label' => 'HTTP',
                        'tooltip' => 'HTTP Headers & Security Settings',
                        'animation' => 'animate-pulse'
                    ]
                ]
            ],
            'DNS & Domain' => [
                'icon' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
                'tooltip' => 'DNS Records, Propagation and Domain Information',
                'animation' => 'animate-spin',
                'items' => [
                    'dns' => [
                        'icon' => 'M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9',
                        'label' => 'DNS',
                        'tooltip' => 'DNS Records & Configuration',
                        'animation' => 'animate-spin'
                    ],
                    'propagation' => [
                        'icon' => 'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z',
                        'label' => 'DNS Propagation',
                        'tooltip' => 'DNS Propagation Status',
                        'animation' => 'animate-ping'
                    ],
                    'registry' => [
                        'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'label' => 'Domain Info',
                        'tooltip' => 'Domain Registry Information',
                        'animation' => 'animate-bounce'
                    ]
                ]
            ],
            'SEO & Analytics' => [
                'icon' => 'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11',
                'tooltip' => 'SEO Analysis, Analytics and Backlinks',
                'animation' => 'animate-bounce',
                'items' => [
                    'seo' => [
                        'icon' => 'M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11',
                        'label' => 'SEO',
                        'tooltip' => 'Search Engine Optimization Analysis',
                        'animation' => 'animate-bounce'
                    ],
                    'analytics' => [
                        'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                        'label' => 'Analytics',
                        'tooltip' => 'Traffic & User Analytics',
                        'animation' => 'animate-ping'
                    ],
                    'backlinks' => [
                        'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
                        'label' => 'Backlinks',
                        'tooltip' => 'Backlink Analysis & Quality',
                        'animation' => 'animate-pulse'
                    ]
                ]
            ],
            'Technical' => [
                'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                'tooltip' => 'Performance, Robots.txt and Sitemap Analysis',
                'animation' => 'animate-ping',
                'items' => [
                    'performance' => [
                        'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                        'label' => 'Performance',
                        'tooltip' => 'Performance Metrics & Analysis',
                        'animation' => 'animate-ping'
                    ],
                    'robots' => [
                        'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
                        'label' => 'Robots',
                        'tooltip' => 'Robots.txt Analysis',
                        'animation' => 'animate-ping'
                    ],
                    'sitemap' => [
                        'icon' => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
                        'label' => 'Sitemap',
                        'tooltip' => 'Sitemap Structure & Coverage',
                        'animation' => 'animate-spin'
                    ]
                ]
            ],
            'Compliance & UX' => [
                'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                'tooltip' => 'Accessibility, Mobile, Cookies and OpenGraph Analysis',
                'animation' => 'animate-pulse',
                'items' => [
                    'accessibility' => [
                        'icon' => 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z',
                        'label' => 'A11y',
                        'tooltip' => 'Accessibility Compliance Check',
                        'animation' => 'animate-pulse'
                    ],
                    'mobile' => [
                        'icon' => 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z',
                        'label' => 'Mobile',
                        'tooltip' => 'Mobile Responsiveness Analysis',
                        'animation' => 'animate-bounce'
                    ],
                    'cookies' => [
                        'icon' => 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z',
                        'label' => 'Cookies',
                        'tooltip' => 'Cookie Compliance & Settings',
                        'animation' => 'animate-bounce'
                    ],
                    'opengraph' => [
                        'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z',
                        'label' => 'OpenGraph',
                        'tooltip' => 'OpenGraph Analysis',
                        'animation' => 'animate-ping'
                    ]
                ]
            ]
        ];
        @endphp

        @foreach($menuGroups as $groupName => $config)
            <div x-cloak x-data="{ open: false }">
                <button 
                    @click="open = !open; activeGroup = open ? '{{ $groupName }}' : null"
                    @click.away="open = false"
                    class="group relative px-4 py-2 rounded-xl text-md font-medium transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 bg-white/80 dark:bg-gray-800/80 text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/50 shadow-md hover:shadow-lg"
                >
                    <span class="flex items-center gap-2" :class="{ 'text-blue-600 dark:text-blue-400': activeGroup === '{{ $groupName }}' || Object.keys({{ json_encode($config['items']) }}).includes(activeTab) }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" 
                            :class="{ '{{ $config['animation'] }}': activeGroup === '{{ $groupName }}' || Object.keys({{ json_encode($config['items']) }}).includes(activeTab) }">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"/>
                        </svg>
                        {{ $groupName }}
                        <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open, 'text-blue-500': activeGroup === '{{ $groupName }}' || Object.keys({{ json_encode($config['items']) }}).includes(activeTab) }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </span>

                    <div class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none transform group-hover:rotate-2 group-hover:scale-105">
                        <div class="relative bg-gradient-to-br from-white/95 via-blue-50/95 to-indigo-50/95 dark:from-gray-800/95 dark:via-blue-900/95 dark:to-indigo-900/95 text-gray-800 dark:text-gray-200 text-sm px-8 py-4 rounded-xl shadow-[0_0_15px_rgba(59,130,246,0.5)] border border-blue-200/50 dark:border-blue-700/50 backdrop-blur-md min-w-[300px]">
                            <div class="absolute inset-0 bg-blue-400/10 dark:bg-blue-400/5 rounded-xl animate-pulse"></div>
                            <div class="relative flex items-start gap-6">
                                <div class="absolute -left-3 -top-3">
                                    <svg class="w-12 h-12 text-blue-500 dark:text-blue-400 animate-bounce filter drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $config['icon'] }}"/>
                                    </svg>
                                </div>
                                <div class="pl-10">
                                    <span class="font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">{{ $config['tooltip'] }}</span>
                                </div>
                            </div>
                            <div class="absolute -inset-px bg-gradient-to-r from-blue-400/30 to-indigo-400/30 blur opacity-50 group-hover:opacity-75 transition-opacity rounded-xl"></div>
                        </div>
                        <div class="w-4 h-4 bg-gradient-to-br from-white/95 to-blue-50/95 dark:from-gray-800/95 dark:to-blue-900/95 transform rotate-45 absolute left-1/2 -translate-x-1/2 -bottom-2 border-r border-b border-blue-200/50 dark:border-blue-700/50 shadow-lg"></div>
                    </div>
                </button>

                <div 
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute z-[100] mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700"
                >
                    <div class="p-2 space-y-1">
                        @foreach($config['items'] as $tab => $item)
                            <button
                                @click="activeTab = '{{ $tab }}'; open = false"
                                @mouseenter="hoverTab = '{{ $tab }}'"
                                @mouseleave="hoverTab = null"
                                :class="{
                                    'bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-600 dark:from-blue-400 dark:via-purple-400 dark:to-indigo-500 text-white shadow-lg scale-105': activeTab === '{{ $tab }}',
                                    'hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-blue-900/50 dark:hover:to-indigo-900/50 hover:scale-102': activeTab !== '{{ $tab }}'
                                }"
                                class="group relative w-full px-4 py-2 rounded-lg text-left text-sm font-medium transition-all duration-300 ease-in-out transform hover:shadow-md flex items-center gap-2 overflow-hidden"
                            >
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-400/20 to-indigo-400/20 dark:from-blue-500/20 dark:to-indigo-500/20 animate-pulse opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                
                                <svg class="w-5 h-5 transform transition-transform duration-300 group-hover:rotate-12" 
                                    fill="none" 
                                    stroke="currentColor" 
                                    viewBox="0 0 24 24"
                                    :class="{ '{{ $item['animation'] }} scale-110': activeTab === '{{ $tab }}' }">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                </svg>
                                
                                <span class="relative z-10 font-semibold tracking-wide">{{ __($item['label']) }}</span>

                                <div class="absolute left-full ml-2 w-48 opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none transform group-hover:translate-x-1">
                                    <div class="bg-gradient-to-br from-white/95 via-blue-50/95 to-indigo-50/95 dark:from-gray-800/95 dark:via-blue-900/95 dark:to-indigo-900/95 text-gray-900 dark:text-white text-sm px-4 py-2 rounded-lg shadow-xl border border-blue-200/50 dark:border-blue-800/50 backdrop-blur-sm">
                                        <span class="font-medium">{{ $item['tooltip'] }}</span>
                                        <div class="absolute -inset-px bg-gradient-to-r from-blue-400/20 to-indigo-400/20 blur opacity-50 rounded-lg"></div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </nav>
</div>