@props([
    'title',
    'items',
    'total' => null,
    'route' => null
])

<div class="bg-gradient-to-br from-indigo-900 to-indigo-950 rounded-xl shadow-lg border border-indigo-800 p-6">
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <div class="relative">
                    <div class="absolute inset-0 bg-indigo-800/20 blur-xl rounded-full"></div>
                    <div class="relative bg-gradient-to-br from-indigo-700 to-indigo-900 p-3 rounded-full">
                        <x-icon name="heroicon-o-list-bullet" class="w-6 h-6 text-indigo-100" />
                    </div>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-indigo-100">
                {{ $title }}
            </h3>
        </div>
        @if($route)
            <a href="{{ $route }}" class="text-sm font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                {{ __('View all') }}
            </a>
        @endif
    </div>

    <div class="space-y-4">
        <ul role="list" class="divide-y divide-indigo-800">
            @forelse($items as $item)
                <li class="py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-indigo-100 truncate">
                                {{ $item->value ?: __('Unknown') }}
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-700/50 text-indigo-200">
                                {{ number_format($item->count) }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="py-4">
                    <p class="text-sm text-indigo-300 text-center">
                        {{ __('No data available') }}
                    </p>
                </li>
            @endforelse
        </ul>
    </div>

    @if($total)
        <div class="mt-6">
            <p class="text-sm text-indigo-300">
                {{ __('Total') }}: {{ number_format($total) }}
            </p>
        </div>
    @endif
</div>
