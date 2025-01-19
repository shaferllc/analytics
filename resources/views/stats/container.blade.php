<x-app>
    <div class="bg-gray-100 dark:bg-gray-900 flex-grow min-h-screen">
        <div class="container mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    @include('analytics::stats.header', ['website' => $site])
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700">
                    <div class="p-6">
                        @include('analytics::stats.' . $view)
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('Report generated on :date at :time (UTC :offset).', [
                            'date' => now()->format(__('Y-m-d')),
                            'time' => now()->format('H:i:s'),
                            'offset' => now()->getOffsetString()
                        ]) }}
                        <a href="{{ Request::fullUrl() }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 ml-2">{{ __('Refresh report') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('analytics::shared.sidebars.user', ['website' => $site])
</x-app>
