<x-app>
    <div class="bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-900 dark:to-gray-800 flex-grow min-h-screen">
        <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-2xl rounded-2xl  transition-all duration-300 hover:shadow-3xl">
               

                <div class=" min-h-[500px]">
                    <div class="p-6">
                        {{ $slot }}
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 flex justify-between items-center">
                        <span>
                            {{ __('Report generated on :date at :time (UTC :offset).', [
                                'date' => now()->format(__('Y-m-d')),
                                'time' => now()->format('H:i:s'),
                                'offset' => now()->getOffsetString()
                            ]) }}
                        </span>
                        <a href="{{ Request::fullUrl() }}" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors duration-200">{{ __('Refresh report') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</x-app>