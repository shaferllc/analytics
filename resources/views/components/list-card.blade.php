@props([
    'title',
    'items',
    'total' => null,
    'route' => null,
    'icon' => 'heroicon-o-list-bullet',
    'type' => 'default', // New prop to determine the type of list (browser, os, device, etc.)
    'translate' => true
])


<div class="bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-2">
            <x-icon :name="$icon" class="w-6 h-6 text-slate-400" />
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                {{ $title }}
            </h3>
        </div>
        @if($route)
            <a href="{{ $route }}" class="text-sm font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                {{ __('View all') }}
            </a>
        @endif
    </div>

    <div class="space-y-3">
        <ul role="list" class="divide-y divide-slate-200/60 dark:divide-slate-700/60">

            @forelse($items as $item)
                <li class="py-3 hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors px-2 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            @if($type === 'browser')
                                <img src="/vendor/analytics/icons/browsers/{{ formatBrowser($item['name']) }}.svg" class="w-5 h-5 text-slate-400" />
                            @elseif($type === 'os')
                                <img src="/vendor/analytics/icons/os/{{ formatOperatingSystem($item['name']) }}.svg" class="w-5 h-5 text-slate-400" />
                            @elseif($type === 'device')
                                <img src="/vendor/analytics/icons/devices/{{ formatDevice($item['name']) }}.svg" class="w-5 h-5 text-slate-400" />
                            @elseif($type === 'country')
                                <img src="https://flagcdn.com/w40/{{ formatFlag($item['name']) }}.png" class="w-5 h-5 text-slate-400" />
                            @elseif($type === 'referrer')
                                <img src="https://www.google.com/s2/favicons?domain={{ $item['name'] }}" class="w-5 h-5 text-slate-400" />
                            @endif
                            <p class="text-sm font-medium text-slate-900 dark:text-slate-100 truncate">
                                {{ $translate ? translateAnalyticData($item['name']) : $item['name'] }}
                            </p>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100/50 dark:bg-slate-700/20 text-slate-700 dark:text-slate-300">
                                {{ $item['total'] }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="py-3">
                    <p class="text-sm text-slate-500 dark:text-slate-400 text-center">
                        {{ __('No data available') }}
                    </p>
                </li>
            @endforelse
        </ul>
    </div>

    @if($total)
        <div class="mt-6 pt-4 border-t border-slate-200/60 dark:border-slate-700/60">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $total }}</span>
            </p>
        </div>
    @endif

    <x-pagination :paginator="$items" :scrollTo="false" type="compact" />
</div>
