<div x-data="{ showFilters: false }" class="relative z-50">
    <div class="flex flex-col lg:flex-row gap-4 p-6 bg-gradient-to-br from-white/90 to-white/70 dark:from-gray-800/90 dark:to-gray-800/70 backdrop-blur-xl rounded-3xl shadow-2xl border border-blue-100/50 dark:border-blue-900/50 hover:shadow-3xl hover:scale-[1.01] transition-all duration-500">
        <!-- Search -->
        <div class="flex-1">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <x-icon name="heroicon-o-magnifying-glass" class="h-5 w-5 text-gray-400 group-hover:text-blue-500 transition-colors duration-300 animate-pulse"/>
                </div>
                <input 
                    type="text" 
                    wire:model.debounce.500ms.live="search"
                    placeholder="{{ __('Search domains & certificates...') }}"
                    class="block w-full pl-12 pr-4 py-4 rounded-2xl border-2 border-blue-100 dark:border-blue-900 bg-white/50 dark:bg-gray-800/50 focus:ring-4 focus:ring-blue-300/30 focus:border-blue-400 hover:border-blue-300 dark:hover:border-blue-700 transition-all duration-300"
                >
                <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-blue-400/0 via-blue-400/10 to-blue-400/0 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-700 animate-gradient-x"></div>
            </div>
        </div>

        <!-- Filters Toggle -->
        <button 
            @click="showFilters = !showFilters"
            class="px-6 py-4 rounded-2xl bg-blue-500 hover:bg-blue-600 text-white font-medium shadow-lg hover:shadow-xl transition-all duration-300 flex items-center gap-2"
        >
            <x-icon name="heroicon-o-funnel" class="w-5 h-5" />
            <span>{{ __('Filters') }}</span>
        </button>

        <!-- Animated Filters Panel -->
        <div 
            x-show="showFilters"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-4"
            @click.away="showFilters = false"
            class="fixed lg:absolute top-20 lg:top-full left-0 right-0 mt-4 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-blue-100/50 dark:border-blue-900/50 z-[100]"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <select 
                    wire:model.debounce.500ms.live="filter"
                    class="w-full px-4 py-3 rounded-xl border-2 border-blue-100 dark:border-blue-900 bg-white/50 dark:bg-gray-800/50 focus:ring-4 focus:ring-blue-300/30 focus:border-blue-400 transition-all duration-300"
                >
                    <option value="all">{{ __('All Certificates') }}</option>
                    <option value="with_cert">{{ __('With Certificate') }}</option>
                    <option value="without_cert">{{ __('Without Certificate') }}</option>
                    <option value="valid">{{ __('Valid') }}</option>
                    <option value="expiring">{{ __('Expiring Soon') }}</option>
                    <option value="expired">{{ __('Expired') }}</option>
                </select>

                <div class="flex gap-2">
                    <select 
                        wire:model.debounce.500ms.live="sortBy"
                        class="w-full px-4 py-3 rounded-xl border-2 border-blue-100 dark:border-blue-900 bg-white/50 dark:bg-gray-800/50 focus:ring-4 focus:ring-blue-300/30 focus:border-blue-400 transition-all duration-300"
                    >
                        <option value="domain">{{ __('Sort by Domain') }}</option>
                        <option value="valid_to">{{ __('Sort by Expiry') }}</option>
                        <option value="grade">{{ __('Sort by Grade') }}</option>
                        <option value="has_cert">{{ __('Sort by Status') }}</option>
                    </select>

                    <button 
                        wire:click="sort"
                        class="px-6 py-3 rounded-xl border-2 border-blue-100 dark:border-blue-900 bg-white/50 dark:bg-gray-800/50 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all duration-300"
                        title="{{ $sortDesc ? __('Sort Descending') : __('Sort Ascending') }}"
                    >
                        <x-icon :name="$sortDesc ? 'heroicon-o-arrow-down' : 'heroicon-o-arrow-up'" class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>