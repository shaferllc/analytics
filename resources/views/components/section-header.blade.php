@props([
    'title' => null,
    'description' => null,
    'icon' => null,
])
<div class="cursor-pointer" @click="isOpen = !isOpen">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-2 flex-initial">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg">
                    <x-icon :name="$icon" class="w-6 h-6 text-emerald-500" />
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100">
                        {{ $title }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        {{ $description }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 p-2 hover:bg-slate-100/50 dark:hover:bg-slate-700/20 rounded-lg transition-colors">
            <div>
                <template x-if="isOpen">
                    <x-icon name="heroicon-o-chevron-up" class="w-6 h-6 text-slate-400 bg-slate-100/50 dark:bg-slate-500/20 rounded-lg p-1" />
                </template>
                <template x-if="!isOpen">
                    <x-icon name="heroicon-o-chevron-down" class="w-6 h-6 text-slate-400 bg-slate-100/50 dark:bg-slate-500/20 rounded-lg p-1" />
                </template>
            </div>
        </div>
    </div>
</div>
