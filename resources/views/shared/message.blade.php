@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-dismiss="alert" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-green-500 hover:text-green-600">
                <x-icon name="heroicon-o-x-circle" class="h-5 w-5" />
            </span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
        <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" data-dismiss="alert" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="text-red-500 hover:text-red-600">
                <x-icon name="heroicon-o-x-circle" class="h-5 w-5" />
            </span>
        </button>
    </div>
@endif