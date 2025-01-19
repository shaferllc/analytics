<div class="bg-gradient-to-r from-gray-800 via-black to-gray-900 rounded-2xl shadow-lg border-2 border-gray-700 p-8">
    <div class="flex flex-col items-center space-y-6">
        <div class="relative">
            <div class="absolute inset-0 bg-amber-400/20 blur-xl rounded-full animate-pulse"></div>
            <div class="relative bg-gradient-to-br from-gray-700 to-gray-900 p-4 rounded-full shadow-lg group hover:rotate-[360deg] transition-transform duration-1000">
                <x-icon name="heroicon-o-light-bulb" class="w-12 h-12 text-amber-400 animate-[bounce_2s_ease-in-out_infinite] group-hover:text-amber-300 transition-colors" />
            </div>
        </div>
        <div class="text-center">
            <h3 class="text-2xl font-bold text-white mb-3 animate-pulse">{{ __('No data yet!') }} 🤔</h3>
            <p class="text-gray-400 mb-4 hover:text-gray-300 transition-colors">{{ __('We haven\'t collected any data for this time period yet.') }}</p>

            <div class="bg-gray-800/50 rounded-lg p-4 max-w-md mx-auto transform hover:-rotate-1 hover:scale-105 transition-all duration-300 hover:bg-gray-800/70">
                <h4 class="text-amber-400 font-medium mb-2 group-hover:text-amber-300">{{ __('Quick Tips:') }}</h4>
                <ul class="text-sm text-gray-300 space-y-3">
                    <li class="flex items-center p-2 rounded-lg hover:bg-gray-700/50 transition-all group cursor-pointer hover:translate-x-2">
                        <x-icon name="heroicon-o-calendar" class="w-4 h-4 mr-2 text-amber-500 group-hover:rotate-[360deg] transition-transform duration-500" />
                        <span>{{ __('Try adjusting your date range') }}</span>
                    </li>
                    <li class="flex items-center p-2 rounded-lg hover:bg-gray-700/50 transition-all group cursor-pointer hover:translate-x-2">
                        <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 mr-2 text-amber-500 group-hover:animate-spin transition-transform" />
                        <span>{{ __('Check back in a few hours') }}</span>
                    </li>
                    <li class="flex items-center p-2 rounded-lg hover:bg-gray-700/50 transition-all group cursor-pointer hover:translate-x-2">
                        <x-icon name="heroicon-o-code-bracket" class="w-4 h-4 mr-2 text-amber-500 group-hover:scale-125 group-hover:animate-pulse transition-transform" />
                        <span>{{ __('Verify your tracking code is installed correctly') }} </span>
                    </li>
                </ul>
            </div>

            <div class="mt-6 text-sm text-gray-400 animate-bounce">
                <span class="inline-block hover:rotate-12 transition-transform cursor-default"></span>
                <span class="hover:text-amber-400 transition-colors">{{ __('Exciting data coming soon!') }}</span>
                <span class="inline-block hover:-rotate-12 transition-transform cursor-default">✨</span>
            </div>
        </div>
    </div>
</div>
