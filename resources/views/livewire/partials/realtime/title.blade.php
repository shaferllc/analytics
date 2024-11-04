<div class="flex flex-col lg:flex-row items-start lg:items-center px-4 lg:px-6 py-4 bg-gradient-to-r from-emerald-50/50 to-teal-50/50 dark:from-gray-800/50 dark:to-gray-700/50 rounded-xl border border-emerald-100 dark:border-gray-700 shadow-lg">
    <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-8 w-full">
        <div class="relative mb-4 lg:mb-0">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-teal-500 opacity-20 rounded-xl animate-pulse"></div>
            <div class="relative p-4 lg:p-5 bg-white/50 dark:bg-gray-800/50 rounded-xl shadow-lg backdrop-blur-sm">
                <x-icon name="heroicon-o-chart-bar" class="w-10 h-10 lg:w-12 lg:h-12 text-emerald-600 dark:text-emerald-400 transform hover:rotate-6 transition-transform" />
            </div>
        </div>
        <div class="flex-1">
            <h1 class="text-2xl lg:text-3xl font-black bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400 bg-clip-text text-transparent">
                {{ __('Real-time Analytics') }} 🚀
            </h1>
            <p class="block text-base lg:text-lg font-medium text-emerald-700 dark:text-emerald-300 mt-1">
                {{ __('Watch your visitors explore in real-time') }} ✨
            </p>
            <div class="flex flex-wrap items-center gap-2 lg:gap-4 mt-3 text-xs lg:text-sm">
                <button wire:click="$refresh" class="flex items-center px-2 lg:px-3 py-1 lg:py-1.5 bg-white/70 dark:bg-gray-800/70 rounded-full shadow-sm backdrop-blur-sm hover:bg-emerald-100 dark:hover:bg-emerald-800/50 transition-colors">
                    <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 lg:w-4 lg:h-4 text-emerald-500 dark:text-emerald-400 mr-1 lg:mr-2" />
                    <span class="text-emerald-700 dark:text-emerald-300">{{ __('Refresh Now') }}</span>
                </button>
                <div x-data="{ isOpen: false }" class="flex items-center px-2 lg:px-3 py-1 lg:py-1.5 bg-white/70 dark:bg-gray-800/70 rounded-full shadow-sm backdrop-blur-sm">
                    <x-icon name="heroicon-o-arrow-path" class="w-3 h-3 lg:w-4 lg:h-4 text-emerald-500 dark:text-emerald-400 mr-1 lg:mr-2" />
                    <span class="text-emerald-700 dark:text-emerald-300">
                        {{ __('Updates every') }} {{ $interval }}s
                    </span>
                    <div class="relative ml-1 lg:ml-2">
                        <button @click="isOpen = !isOpen" class="p-1 hover:bg-emerald-100 dark:hover:bg-emerald-800/50 rounded-full transition-colors">
                            <x-icon name="heroicon-o-chevron-down" class="w-3 h-3 lg:w-4 lg:h-4 text-emerald-500 dark:text-emerald-400" />
                        </button>
                        <div x-show="isOpen" 
                            @click.away="isOpen = false"
                            class="absolute right-0 mt-2 w-28 lg:w-32 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-10">
                            <template x-for="i in [1,5,10,15,30,60]">
                                <button @click="isOpen = false; $wire.set('interval', i)" 
                                    class="block w-full px-3 lg:px-4 py-1.5 lg:py-2 text-left hover:bg-emerald-50 dark:hover:bg-emerald-800/30 text-emerald-700 dark:text-emerald-300"
                                    x-text="`${i} seconds`">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
                <div x-data="{ nextUpdate: $wire.interval }" 
                    x-init="setInterval(() => { nextUpdate = nextUpdate > 0 ? nextUpdate - 1 : $wire.interval }, 1000)" 
                    class="flex items-center px-2 lg:px-3 py-1 lg:py-1.5 bg-white/70 dark:bg-gray-800/70 rounded-full shadow-sm backdrop-blur-sm">
                    <x-icon name="heroicon-o-clock" class="w-3 h-3 lg:w-4 lg:h-4 text-emerald-500 dark:text-emerald-400 mr-1 lg:mr-2" />
                    <span class="text-emerald-700 dark:text-emerald-300">{{ __('Next update in') }}: <span x-text="nextUpdate" class="font-semibold"></span>s</span>
                </div>
                <div x-data="{ isTimeRangeOpen: false }" class="flex items-center px-2 lg:px-3 py-1 lg:py-1.5 bg-white/70 dark:bg-gray-800/70 rounded-full shadow-sm backdrop-blur-sm">
                    <x-icon name="heroicon-o-clock" class="w-3 h-3 lg:w-4 lg:h-4 text-emerald-500 dark:text-emerald-400 mr-1 lg:mr-2" />
                    <span class="text-emerald-700 dark:text-emerald-300">
                        {{ __('Last') }} {{ $subMinutes }} {{ __('minutes') }}
                    </span>
                    <div class="relative ml-1 lg:ml-2">
                        <button @click="isTimeRangeOpen = !isTimeRangeOpen" class="p-1 hover:bg-emerald-100 dark:hover:bg-emerald-800/50 rounded-full transition-colors">
                            <x-icon name="heroicon-o-chevron-down" class="w-3 h-3 lg:w-4 lg:h-4 text-emerald-500 dark:text-emerald-400" />
                        </button>
                        <div x-show="isTimeRangeOpen" 
                            @click.away="isTimeRangeOpen = false"
                            class="absolute right-0 mt-2 w-28 lg:w-32 bg-white dark:bg-gray-800 rounded-lg shadow-lg py-1 z-50">
                            <template x-for="i in [1,5,15,30,60]">
                                <button @click="isTimeRangeOpen = false; $wire.set('subMinutes', i)" 
                                    class="block w-full px-3 lg:px-4 py-1.5 lg:py-2 text-left hover:bg-emerald-50 dark:hover:bg-emerald-800/30 text-emerald-700 dark:text-emerald-300"
                                    x-text="`${i} minutes`">
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
                <div class="flex items-center px-2 lg:px-3 py-1 lg:py-1.5 bg-white/70 dark:bg-gray-800/70 rounded-full shadow-sm backdrop-blur-sm">
                    <x-icon name="heroicon-o-calendar" class="w-3 h-3 lg:w-4 lg:h-4 text-emerald-500 dark:text-emerald-400 mr-1 lg:mr-2" />
                    <span class="text-emerald-700 dark:text-emerald-300 hidden lg:inline">{{ $from->format('g:i:s A T') }}</span>
                    <span class="text-emerald-700 dark:text-emerald-300 lg:hidden">{{ $from->format('g:i A') }}</span>
                    <span class="mx-1 lg:mx-2 text-emerald-400">→</span>
                    <x-icon name="heroicon-o-calendar" class="w-3 h-3 lg:w-4 lg:h-4 text-emerald-500 dark:text-emerald-400 mx-1 lg:mx-2" />
                    <span class="text-emerald-700 dark:text-emerald-300 hidden lg:inline">{{ $to->format('g:i:s A T') }}</span>
                    <span class="text-emerald-700 dark:text-emerald-300 lg:hidden">{{ $to->format('g:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
