<x-app>
    <div class="bg-gray-100 flex-grow">
        <div class="container mx-auto py-8">
            <div class="max-w-4xl mx-auto">
                <h1 class="text-3xl font-bold mb-6">{{ __('New') }}</h1>

                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-xl font-semibold">{{ __('Website') }}</h2>
                    </div>

                    <div class="p-6">
                        @include('analytics::shared.message')

                        <form action="{{ route('websites.new') }}" method="post" enctype="multipart/form-data" id="form-website">
                            @csrf

                            <div class="mb-6">
                                <label for="i-domain" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Domain') }}</label>
                                <input type="text" dir="ltr" name="domain" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('domain') border-red-500 @enderror" id="i-domain" value="{{ old('domain') }}" placeholder="example.com">
                                @error('domain')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-sm text-gray-500">{{ __('Add a domain or subdomain.') }}</p>
                            </div>

                            <hr class="my-6">

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Privacy') }}</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio" name="privacy" value="1" @if(old('privacy') == null || old('privacy') == 1) checked @endif>
                                            <span class="ml-2">
                                                <span class="text-sm font-medium text-gray-900">{{ __('Private') }}</span><br>
                                                <span class="text-xs text-gray-500">{{ __('Stats accessible only by you.') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio" name="privacy" value="0" @if(old('privacy') == 0 && old('privacy') != null) checked @endif>
                                            <span class="ml-2">
                                                <span class="text-sm font-medium text-gray-900">{{ __('Public') }}</span><br>
                                                <span class="text-xs text-gray-500">{{ __('Stats accessible by anyone.') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="radio" class="form-radio" name="privacy" value="2" @if(old('privacy') == 2) checked @endif>
                                            <span class="ml-2">
                                                <span class="text-sm font-medium text-gray-900">{{ __('Password') }}</span><br>
                                                <span class="text-xs text-gray-500">{{ __('Stats accessible by password.') }}</span>
                                            </span>
                                        </label>
                                        <div id="input-password" class="{{ (old('privacy') != 2 ? 'hidden' : '')}} mt-2">
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">
                                                        <x-icon name="heroicon-o-lock-closed" class="w-5 h-5" />
                                                    </span>
                                                </div>
                                                <input type="password" name="password" id="i-password" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Password">
                                            </div>
                                            @error('password')
                                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @error('privacy')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <hr class="my-6">

                            <div class="mb-6">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">{{ __('Notifications') }}</label>

                                </div>
                                <div class="bg-gray-50 p-4 rounded-md">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" class="form-checkbox" name="email" value="1" @if(old('email')) checked @endif @cannot('emailReports', ['App\Models\User']) disabled @endcannot>
                                        <span class="ml-2">
                                            <span class="text-sm font-medium text-gray-900">{{ __('Email') }}</span><br>
                                            <span class="text-xs text-gray-500">{{ __('Periodic email reports.') }}</span>
                                        </span>
                                    </label>
                                    @error('email')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <hr class="my-6">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="i-exclude-ips" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('Exclude IPs') }}
                                        <span class="ml-1 text-gray-500" title="{{ __('To block entire IP classes, use the CIDR notation.') }}">
                                            <x-icon name="heroicon-o-information-circle" class="inline-block w-4 h-4" />
                                        </span>
                                    </label>
                                    <textarea name="exclude_ips" id="i-exclude-ips" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md @error('exclude_ips') border-red-500 @enderror">{{ old('exclude_ips') }}</textarea>
                                    @error('exclude_ips')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">{{ __('One per line.') }}</p>
                                </div>

                                <div>
                                    <label for="i-exclude-params" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Exclude URL query parameters') }}</label>
                                    <textarea name="exclude_params" id="i-exclude-params" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md @error('exclude_params') border-red-500 @enderror">{{ old('exclude_params') }}</textarea>
                                    @error('exclude_params')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-2 text-sm text-gray-500">{{ __('One per line.') }}</p>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="form-checkbox" name="exclude_bots" value="1" @if(old('exclude_bots') || old('exclude_bots') == null) checked @endif>
                                    <span class="ml-2">
                                        <span class="text-sm font-medium text-gray-900">{{ __('Exclude bots') }}</span><br>
                                        <span class="text-xs text-gray-500">{{ __('Exclude common bots from being tracked.') }}</span>
                                    </span>
                                </label>
                                @error('exclude_bots')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <hr class="my-6">

                            <div class="mb-6">
                                @include('analytics::shared.tracking-code')
                            </div>

                            <div>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{ __('Save') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('analytics::shared.sidebars.user')
</x-app>