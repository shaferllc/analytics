    <div class="bg-gray-100 flex items-center flex-grow">
        <div class="container py-3 my-3">
            <div class="flex justify-center">
                <div class="w-full md:w-10/12 lg:w-8/12 xl:w-6/12">
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium">{{ __('Tracking code') }}</div>
                                </div>

                                <div class="flex items-center">
                                    <span class="flex items-center">
                                        <img src="https://icons.duckduckgo.com/ip3/{{ $website->domain }}.ico" rel="noreferrer" class="w-4 h-4 {{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">
                                    </span>
                                    <span class="flex-grow">{{ $website->domain }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-4">
                            @include('analytics::shared.message')

                            <div class="mb-4">
                                @include('analytics::shared.tracking-code')
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('analytics.stats.overview', ['website' => $website]) }}" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded flex items-center justify-center">
                                    {{ __('Start') }}
                                    <x-icon name="heroicon-o-arrow-right" class="w-4 h-4 fill-current" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>