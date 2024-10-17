<div x-data="{ open: false }" class="relative">
    @if(request()->is('admin/*') || request()->is('dashboard') || request()->is('websites/*'))
        <button @click="open = !open" class="flex items-center text-primary hover:text-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50">
            <x-icon name="heroicon-o-ellipsis-vertical" class="w-5 h-5" />
            <span class="sr-only">{{ __('More options') }}</span>
        </button>
    @endif

    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
        <div class="py-1">
            @if(request()->is('admin/*') || (Auth::check() && Auth::user()->role == 1) || (Auth::check() && $website->user_id == Auth::user()->id))
                <a href="{{ request()->is('admin/*') || (Auth::user()->role == 1 && $website->user_id != Auth::user()->id) ? route('admin.websites.edit', $website->id) : route('websites.edit', $website->id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <x-icon name="heroicon-o-pencil" class="w-4 h-4 mr-3 text-gray-400" />
                    {{ __('Edit') }}
                </a>
            @endif

            <a href="{{ route('stats.overview', ['id' => $website->domain, 'from' => $range['from'] ?? null, 'to' => $range['to'] ?? null]) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <x-icon name="eye" class="w-4 h-4 mr-3 text-gray-400" />
                {{ __('View') }}
            </a>

            <a href="{{ 'http://' . $website->domain }}" target="_blank" rel="nofollow noreferrer noopener" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-4 h-4 mr-3 text-gray-400" />
                {{ __('Open') }}
            </a>

            @if(request()->is('admin/*') || (Auth::check() && Auth::user()->role == 1) || (Auth::check() && $website->user_id == Auth::user()->id))
                <div class="border-t border-gray-100"></div>

                <button @click="$dispatch('open-modal', { 
                    action: '{{ request()->is('admin/*') || (Auth::user()->role == 1 && $website->user_id != Auth::user()->id) ? route('admin.websites.destroy', $website->id) : route('websites.destroy', $website->id) }}',
                    title: '{{ __('Delete') }}',
                    text: '{{ __('Are you sure you want to delete :name?', ['name' => $website->domain]) }}',
                    button: 'bg-red-500 hover:bg-red-600 text-white'
                })" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                    <x-icon name="heroicon-o-trash" class="w-4 h-4 mr-3 text-red-400" />
                    {{ __('Delete') }}
                </button>
            @endif
        </div>
    </div>
</div>