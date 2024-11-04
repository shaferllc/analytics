<x-app>
    <div class="bg-gray-100 flex items-center justify-center min-h-screen">
        <div class="container mx-auto px-4 py-16">
            <div class="max-w-md mx-auto">
                <form action="{{ route('analytics.stats.password', ['website' => $website]) }}" method="post" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    @csrf

                    <h1 class="text-2xl mb-3 text-center font-bold">{{ __('Website protected') }}</h1>
                    <p class="mb-5 text-center text-gray-600">{{ __('This website is password protected.') }}</p>

                    <div class="flex mb-5">
                        <div class="flex-grow {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                            <input id="i-password" type="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('password') ? ' border-red-500' : '' }}" name="password">
                            @if ($errors->has('password'))
                                <p class="text-red-500 text-xs italic mt-2">{{ $errors->first('password') }}</p>
                            @endif
                        </div>
                        <div>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">{{ __('Validate') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('analytics::shared.sidebars.user', ['website' => $website])
</x-app>