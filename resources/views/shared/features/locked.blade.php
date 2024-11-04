<div class="h-full flex flex-col justify-center items-center my-20">
    <div class="relative w-32 h-32 flex items-center justify-center">
        <div class="absolute inset-0 bg-primary bg-opacity-10 rounded-full"></div>

        <x-icon name="heroicon-o-lock-closed" class="w-16 h-16 text-primary"/>
    </div>

    <div class="text-center">
        <h5 class="mt-4 text-lg font-semibold">{{ __('Feature locked') }}</h5>
        <p class="text-gray-600">{{ __('Upgrade your account to unlock this feature.') }}</p>

        <div class="mt-5">
            <a href="{{ route('pricing') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">{{ __('Upgrade') }}</a>
        </div>
    </div>
</div>