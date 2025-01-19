<div x-show="activeTab === 'searchEngine'">
    <div class="space-y-4">
        @foreach($searchEngine['data'] as $searchEngineData)
            <div class="flex items-center justify-between p-6 border border-indigo-800 rounded-lg bg-indigo-900/50 hover:bg-indigo-900/70 transition">
                <div class="flex-1">
                    <div class="flex items-center gap-6">
                        <div class="flex-shrink-0">
                            <img src="{{ $searchEngineData->icon }}" class="w-7 h-7" />
                        </div>
                        
                        <div class="min-w-0">
                            <div class="flex items-center gap-3">
                                <h4 class="font-semibold text-lg text-indigo-100">{{ ucfirst($searchEngineData->value) ?: __('Unknown') }}</h4>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-700/50 text-indigo-200">
                                    {{ number_format($searchEngineData->aggregates['total_count'] > 0 ? ($searchEngineData->count / $searchEngineData->aggregates['total_count']) * 100 : 0, 1) }}%
                                </span>
                            </div>

                            <div class="mt-2 flex flex-wrap gap-x-6 gap-y-2 text-sm text-indigo-400">
                                @if(!empty($searchEngineData->search_engine))
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" class="flex items-center text-indigo-400 hover:text-indigo-300">
                                            <x-icon name="heroicon-o-magnifying-glass" class="w-4 h-4 mr-1.5" />
                                            {{ __('Search Engines') }} ({{ count($searchEngineData->search_engine) }})
                                            <template x-if="open">
                                                <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 ml-1" />
                                            </template>
                                            <template x-if="!open">
                                                <x-icon name="heroicon-o-chevron-up" class="w-4 h-4 ml-1" />
                                            </template>
                                        </button>
                                        
                                        <div x-show="open" 
                                                @click.away="open = false"
                                                x-transition
                                                class="absolute z-10 mt-2 bg-indigo-800 rounded-lg shadow-lg py-2 w-48">
                                            @foreach($searchEngineData->search_engine as $engine)
                                                <div class="px-4 py-2 text-sm text-indigo-200 hover:bg-indigo-700">
                                                    {{ $engine['value'] }}
                                                    <span class="text-indigo-400">({{ number_format($engine['count']) }})</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-8 ml-6">
                    <div class="w-40 h-3 rounded-full bg-indigo-700/30 overflow-hidden">
                        <div style="width:0%" 
                            class="h-full bg-gradient-to-r from-indigo-400 to-indigo-600" 
                            x-data 
                            x-init="setTimeout(() => $el.style.width = '{{ $searchEngineData->aggregates['total_count'] > 0 ? ($searchEngineData->count / $searchEngineData->aggregates['total_count']) * 100 : 0 }}%', 100)">
                        </div>
                    </div>
                    <div class="flex flex-col gap-1 w-32 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <x-icon name="heroicon-o-users" class="w-4 h-4 text-indigo-400" />
                            <span class="font-semibold text-indigo-100">{{ number_format($searchEngineData->count, 0, __('.'), __(',')) }}</span>
                        </div>
                        <div class="flex items-center justify-end gap-2">
                            <x-icon name="heroicon-o-user" class="w-4 h-4 text-indigo-400" />
                            <span class="text-sm text-indigo-300">{{ number_format($searchEngineData->unique_sessions, 0, __('.'), __(',')) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>