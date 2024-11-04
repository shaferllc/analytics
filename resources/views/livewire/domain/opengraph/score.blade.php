<div class="bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 dark:from-indigo-900 dark:via-purple-900 dark:to-pink-900 rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition-all duration-300 border border-indigo-200 dark:border-indigo-800">
    <h3 class="text-2xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-300 dark:to-purple-300 flex items-center">
        <x-icon name="heroicon-o-chart-bar" class="w-7 h-7 mr-3 text-indigo-500 animate-pulse" />
        Score Breakdown
    </h3>
    <div class="space-y-4">
        @foreach($opengraphAnalysis['score_breakdown'] ?? [] as $metric => $score)
            @if(is_array($score))
                <div x-data="{ expanded: false }" class="mb-4">
                    <button @click="expanded = !expanded" class="w-full flex items-center justify-between p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                        <div class="flex items-center">
                            <x-icon name="heroicon-o-folder" class="w-5 h-5 mr-3 text-indigo-500" />
                            <span class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ Str::title(str_replace('_', ' ', $metric)) }}</span>
                        </div>
                        <template x-if="expanded">
                            <x-icon name="heroicon-o-chevron-up" class="w-5 h-5 text-gray-500 transition-transform duration-200" />
                        </template>
                        <template x-if="!expanded">
                            <x-icon name="heroicon-o-chevron-down" class="w-5 h-5 text-gray-500 transition-transform duration-200" />
                        </template>
                    </button>
                    
                    <div x-show="expanded" x-collapse class="ml-4 space-y-3 mt-2">
                        @foreach($score as $subMetric => $subScore)
                            @if(is_array($subScore))
                                <div x-data="{ subExpanded: false }" class="mb-4">
                                    <button @click="subExpanded = !subExpanded" class="w-full flex items-center justify-between p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                                        <div class="flex items-center">
                                            <x-icon name="heroicon-o-folder" class="w-5 h-5 mr-3 text-purple-500" />
                                            <span class="text-gray-800 dark:text-gray-200 font-semibold">{{ Str::title(str_replace('_', ' ', $subMetric)) }}</span>
                                        </div>
                                        <template x-if="subExpanded">
                                            <x-icon name="heroicon-o-chevron-up" class="w-5 h-5 text-gray-500 transition-transform duration-200" />
                                        </template>
                                        <template x-if="!subExpanded">
                                            <x-icon name="heroicon-o-chevron-down" class="w-5 h-5 text-gray-500 transition-transform duration-200" />
                                        </template>
                                    </button>
                                    
                                    <div x-show="subExpanded" x-collapse class="ml-4 space-y-3 mt-2">
                                        @foreach($subScore as $subSubMetric => $subSubScore)
                                            <div class="flex items-center justify-between p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                                                <div class="flex items-center">
                                                    <x-icon name="heroicon-o-check-circle" class="w-5 h-5 mr-3 {{ $subSubScore >= 70 ? 'text-green-500' : ($subSubScore >= 40 ? 'text-yellow-500' : 'text-red-500') }}" />
                                                    <span class="text-gray-700 dark:text-gray-300">{{ Str::title(str_replace('_', ' ', $subSubMetric)) }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <div class="w-24 h-2 bg-gray-200 dark:bg-gray-700 rounded-full mr-3">
                                                        <div class="h-2 rounded-full {{ $subSubScore >= 70 ? 'bg-green-500' : ($subSubScore >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $subSubScore }}%"></div>
                                                    </div>
                                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $subSubScore }}%</span>
                                                    <span class="ml-2 text-sm text-gray-500">({{ $subSubScore >= 70 ? 'Good' : ($subSubScore >= 40 ? 'Fair' : 'Poor') }})</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-between p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                                    <div class="flex items-center">
                                        <x-icon name="heroicon-o-check-circle" class="w-5 h-5 mr-3 {{ $subScore >= 70 ? 'text-green-500' : ($subScore >= 40 ? 'text-yellow-500' : 'text-red-500') }}" />
                                        <span class="text-gray-700 dark:text-gray-300">{{ Str::title(str_replace('_', ' ', $subMetric)) }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-24 h-2 bg-gray-200 dark:bg-gray-700 rounded-full mr-3">
                                            <div class="h-2 rounded-full {{ $subScore >= 70 ? 'bg-green-500' : ($subScore >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $subScore }}%"></div>
                                        </div>
                                        <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $subScore }}%</span>
                                        <span class="ml-2 text-sm text-gray-500">({{ $subScore >= 70 ? 'Good' : ($subScore >= 40 ? 'Fair' : 'Poor') }})</span>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <div class="flex items-center justify-between p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                    <div class="flex items-center">
                        <x-icon name="heroicon-o-check-circle" class="w-5 h-5 mr-3 {{ $score >= 70 ? 'text-green-500' : ($score >= 40 ? 'text-yellow-500' : 'text-red-500') }}" />
                        <span class="text-gray-700 dark:text-gray-300">{{ Str::title(str_replace('_', ' ', $metric)) }}</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-24 h-2 bg-gray-200 dark:bg-gray-700 rounded-full mr-3">
                            <div class="h-2 rounded-full {{ $score >= 70 ? 'bg-green-500' : ($score >= 40 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $score }}%"></div>
                        </div>
                        <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $score }}%</span>
                        <span class="ml-2 text-sm text-gray-500">({{ $score >= 70 ? 'Good' : ($score >= 40 ? 'Fair' : 'Poor') }})</span>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>