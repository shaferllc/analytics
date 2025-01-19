@props([
    'paginator',
    'color' => 'blue',
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
     class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white dark:bg-gray-600 border border-gray-50 dark:border-gray-500 p-4 rounded-xl shadow-lg transition-all duration-300"
     role="region"
     aria-label="{{ __('Results and View Options') }}">
    <div class="flex flex-col w-full sm:w-auto">
        <div class="flex items-center justify-center sm:justify-start space-x-2 group transition-all duration-200" aria-live="polite">
            <span class="text-sm text-gray-500 dark:text-gray-300 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-200">{{ __('Showing') }}</span>
            <span class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-50 bg-blue-50 dark:bg-gray-800 rounded-md group-hover:scale-110 transition-all duration-200">
                {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }}
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-300 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-200">{{ __('of') }}</span>
            <span class="px-2 py-1 text-sm font-medium text-gray-700 dark:text-gray-50 bg-blue-50 dark:bg-gray-800 rounded-md group-hover:scale-110 transition-all duration-200">
                {{ $paginator->total() }}
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-300 group-hover:text-gray-700 dark:group-hover:text-gray-300 transition-colors duration-200">{{ __('results') }}</span>
        </div>
    </div>

    <div class="flex justify-center sm:justify-end w-full sm:w-auto">
        <div class="inline-flex flex-wrap items-center justify-center p-2 gap-2 bg-blue-50 dark:bg-gray-800 rounded-xl border border-gray-50 dark:border-gray-500 shadow-xs hover:shadow-gray-100/20 transition-all duration-300 w-full sm:w-auto"
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
                                'bg-gray-100 dark:bg-gray-500 shadow-lg scale-110': view === '{{ $viewType }}',
                                'hover:bg-gray-50 dark:hover:bg-gray-500 hover:scale-110 hover:rotate-2 hover:ring-2 hover:ring-gray-500': view !== '{{ $viewType }}'
                            }"
                            class="p-3 sm:p-2 rounded-lg transition-all duration-300 relative group focus:outline-none"
                            aria-label="{{ __('Switch to ' . ucfirst($viewType) . ' View') }}"
                            :aria-pressed="view === '{{ $viewType }}'">

                            <x-icon :name="$icon"
                                :class="{
                                    'text-gray-700 dark:text-gray-300': view === '{{ $viewType }}',
                                    'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300': view !== '{{ $viewType }}'
                                }"
                                class="w-6 h-6 sm:w-5 sm:h-5 transition-colors duration-300" />

                        <div class="absolute inset-0 rounded-lg bg-gray-200/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </button>
                </x-tooltip>
            @endforeach
        </div>
    </div>
</div>
