<x-site :site="$site" :title="__('Debug Logs')" :description="__('View real-time debug logs and analytics')" icon="heroicon-o-bug-ant">

    <x-breadcrumbs :breadcrumbs="[
        [
            'url' => route('dashboard'),
            'label' => __('Dashboard'),
            'icon' => 'heroicon-o-home',
        ],
        [
            'url' => route('sites.show', ['site' => $site->id]),
            'label' => $site->name,
            'icon' => 'heroicon-o-building-office-2',
        ],
        [
            'url' => route('sites.analytics.debug', ['site' => $site->id]),
            'label' => __('Debug Logs'),
            'icon' => 'heroicon-o-bug-ant',
        ]
    ]" />

    <div class="w-full max-w-7xl mx-auto mt-4" wire:poll.5s="refreshData">
        <!-- Level Filters -->
        <div class="mb-6 flex gap-2">
            @foreach(['info', 'warning', 'error', 'debug'] as $level)
                <button
                    wire:click="toggleFilterLevel('{{ $level }}')"
                    :class="{
                        'px-3 py-1 text-sm font-medium rounded-full transition-colors': true,
                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-400': '{{ $level }}' === 'info' && $wire.filters.levels.includes('{{ $level }}'),
                        'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-400': '{{ $level }}' === 'warning' && $wire.filters.levels.includes('{{ $level }}'),
                        'bg-rose-100 text-rose-800 dark:bg-rose-900/20 dark:text-rose-400': '{{ $level }}' === 'error' && $wire.filters.levels.includes('{{ $level }}'),
                        'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400': '{{ $level }}' === 'debug' && $wire.filters.levels.includes('{{ $level }}'),
                        'bg-slate-100 text-slate-800 dark:bg-slate-700/20 dark:text-slate-400': !$wire.filters.levels.includes('{{ $level }}')
                    }"
                >
                    {{ ucfirst($level) }}
                </button>
            @endforeach
        </div>

        <!-- Debug Logs Section -->
        <div class="bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6">
            <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2 mb-6">
                <x-icon name="heroicon-o-bug-ant" class="w-6 h-6 text-emerald-500" />
                {{ __('Live Debug Logs') }}

                <div wire:loading>
                    <x-icon name="heroicon-o-arrow-path" class="w-4 h-4 text-slate-500 animate-spin" />
                </div>
            </h2>

            <div class="space-y-4">
                @forelse($this->logs as $log)
                    <div
                        x-data="{ isNew: {{ in_array($log->id, $this->newLogIds) ? 'true' : 'false' }} }"
                        x-init="if(isNew) { setTimeout(() => isNew = false, 10000) }"
                        :class="{
                            'rounded-lg p-4 shadow-sm transition-colors duration-300': true,
                            'bg-emerald-500/20': '{{ $log->level }}' === 'info',
                            'bg-blue-500/20': '{{ $log->level }}' === 'debug',
                            'bg-amber-500/20': '{{ $log->level }}' === 'warning',
                            'bg-rose-500/20': '{{ $log->level }}' === 'error',
                            'border-t-2 border-slate-200 dark:border-slate-900': !isNew
                        }"
                    >
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-300">{{ __('Level') }}</div>
                                <div @class([
                                    'text-lg font-semibold capitalize',
                                    'text-emerald-800 dark:text-emerald-300' => $log->level === 'info',
                                    'text-amber-800 dark:text-amber-300' => $log->level === 'warning',
                                    'text-rose-800 dark:text-rose-300' => $log->level === 'error',
                                    'text-blue-800 dark:text-blue-300' => $log->level === 'debug'
                                ])>
                                    {{ $log->level }}
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-300">{{ __('Timestamp') }}</div>
                                <div class="text-sm text-slate-900 dark:text-slate-100">{{ $log->timestamp }}</div>
                            </div>

                            <div>
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-300">{{ __('Message') }}</div>
                                <div class="text-sm text-slate-900 dark:text-slate-100">
                                    <pre class="bg-slate-100/70 dark:bg-slate-700/30 p-2 rounded text-sm whitespace-normal">{{ $log->message }}</pre>
                                </div>
                            </div>

                            <div>
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-300">{{ __('User Agent') }}</div>
                                <div class="text-sm text-slate-900 dark:text-slate-100">{{ $log->user_agent }}</div>
                            </div>

                            <div>
                                <div class="text-xs font-medium text-slate-500 dark:text-slate-300">{{ __('URL') }}</div>
                                <div class="text-sm text-slate-900 dark:text-slate-100">
                                    @if(filter_var($log->url, FILTER_VALIDATE_URL))
                                        <a href="{{ $log->url }}" target="_blank" class="text-blue-800 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-200 hover:underline">
                                            {{ $log->url }}
                                        </a>
                                    @else
                                        {{ $log->url }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 dark:text-slate-400 py-4">
                        {{ __('No debug logs found') }}
                    </div>
                @endforelse
            </div>
        </div>
        <x-pagination :paginator="$this->logs" />
    </div>
</x-site>
