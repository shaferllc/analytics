<div>
    <div class="flex items-center justify-between">
        @if(isset($site))
            <div class="flex space-x-4">
                <div x-cloak x-data="{ open: false }" class="relative">
                    @if(request()->is('dashboard') || request()->is('analytics/*'))
                        <button @click="open = !open" class="btn bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white border-2 border-gray-700 rounded-lg px-4 py-2 font-bold uppercase tracking-widest shadow-inner hover:from-gray-800 hover:to-gray-900 active:translate-y-px">
                            <span class="flex items-center">
                                <x-icon name="heroicon-o-cog" class="w-5 h-5 mr-2" />
                                <span>{{ __('Settings') }}</span>
                            </span>
                        </button>
                    @endif

                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-4 w-56 bg-gray-900 rounded-xl border-4 border-gray-700 shadow-2xl z-10 transition-all duration-300 transform origin-top-right" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                        <div class="py-2">
                            @isset($range)
                                <a href="{{ route('sites.analytics.overview', ['site' => $site, 'from' => $range['from'] ?? null, 'to' => $range['to'] ?? null]) }}" class="group flex items-center px-6 py-3 text-base font-bold text-gray-400 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                                    <x-icon name="heroicon-o-eye" class="w-5 h-5 mr-3 group-hover:animate-pulse" />
                                    {{ __('View') }}
                                </a>
                            @endisset

                            <a href="{{ 'http://' . $site->domain }}" target="_blank" rel="nofollow noreferrer noopener" class="group flex items-center px-6 py-3 text-base font-bold text-gray-400 hover:bg-gray-700 hover:text-white transition-colors duration-200">
                                <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-5 h-5 mr-3 group-hover:animate-bounce" />
                                {{ __('Open') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
