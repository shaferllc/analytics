@props([
    'paginator',
    'color' => 'emerald',
])

<div x-cloak x-data="{
        views: ['graph', 'grid', 'list', 'compact', 'cards', 'timeline'],
        currentIndex: 0,
        init() {
            this.currentIndex = this.views.indexOf(this.view);
            window.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowRight') {
                    this.currentIndex = (this.currentIndex + 1) % this.views.length;
                    this.view = this.views[this.currentIndex];
                    $wire.set('analytics_display', this.view);
                } else if (e.key === 'ArrowLeft') {
                    this.currentIndex = (this.currentIndex - 1 + this.views.length) % this.views.length;
                    this.view = this.views[this.currentIndex];
                    $wire.set('analytics_display', this.view);
                }
            });
        }
    }"
     class="w-full max-w-7xl mx-auto bg-white/90 dark:bg-slate-800/90 rounded-2xl shadow-lg border border-slate-200/60 dark:border-slate-700/60 p-6"
     role="region"
     aria-label="{{ __('Results and View Options') }}">

    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex flex-col w-full sm:w-auto">
            <div class="flex items-center justify-center sm:justify-start space-x-2" aria-live="polite">
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('Showing') }}</span>
                <span class="px-2 py-1 text-sm font-medium text-slate-900 dark:text-slate-100 bg-slate-50/50 dark:bg-slate-700/20 rounded-md">
                    {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }}
                </span>
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('of') }}</span>
                <span class="px-2 py-1 text-sm font-medium text-slate-900 dark:text-slate-100 bg-slate-50/50 dark:bg-slate-700/20 rounded-md">
                    {{ $paginator->total() }}
                </span>
                <span class="text-sm text-slate-500 dark:text-slate-400">{{ __('results') }}</span>
            </div>
        </div>

        <div class="flex justify-center sm:justify-end w-full sm:w-auto">
            <div class="inline-flex items-center p-2 gap-2 bg-slate-50/50 dark:bg-slate-700/20 rounded-xl border border-slate-200/60 dark:border-slate-700/60"
                 role="group"
                 aria-label="{{ __('View Options') }}">

                @foreach([
                    'list' => 'heroicon-o-bars-4',
                    'compact' => 'heroicon-o-table-cells',
                    'cards' => 'heroicon-o-rectangle-stack',
                ] as $viewType => $icon)
                    <x-tooltip text="{{ __(ucfirst($viewType) . ' View') }}">
                        <button wire:click="$set('analytics_display', '{{ $viewType }}'), view = '{{ $viewType }}'"
                                x-data="{ isHovered: false }"
                                @mouseenter="isHovered = true"
                                @mouseleave="isHovered = false"
                                :class="{
                                    'bg-white/50 dark:bg-slate-700/30 shadow-lg': view === '{{ $viewType }}',
                                    'hover:bg-white/50 dark:hover:bg-slate-700/30': view !== '{{ $viewType }}'
                                }"
                                class="p-2 rounded-lg transition-all duration-300 relative focus:outline-none"
                                aria-label="{{ __('Switch to ' . ucfirst($viewType) . ' View') }}"
                                :aria-pressed="view === '{{ $viewType }}'">

                                <x-icon :name="$icon"
                                    class="{
                                        'text-slate-900 dark:text-slate-100': view === '{{ $viewType }}',
                                        'text-slate-500 dark:text-slate-400': view !== '{{ $viewType }}'
                                    }"
                                    class="w-5 h-5 transition-colors duration-300" />
                        </button>
                    </x-tooltip>
                @endforeach
            </div>
        </div>
    </div>
</div>
