   <!-- OpenGraph -->
   <div class="bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 dark:from-indigo-900 dark:via-purple-900 dark:to-pink-900 rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition-all duration-300 border border-indigo-200 dark:border-indigo-800">
        <h3 class="text-2xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-300 dark:to-purple-300 flex items-center">
            <x-icon name="heroicon-o-star" class="w-7 h-7 mr-3 text-indigo-500 animate-pulse" />
            OpenGraph
        </h3>
        <div class="space-y-4">
            @foreach($opengraphAnalysis['@graph'] as $label => $value)
                <div x-data="{ open: false }" class="space-y-2 p-4 bg-white/50 dark:bg-gray-800/50 rounded-lg hover:bg-white/80 dark:hover:bg-gray-800/80 transition-all duration-300 border-l-4 border-indigo-400 hover:border-l-8 transform hover:-translate-y-1">
                    <div @click="open = !open" class="flex items-center justify-between text-gray-700 dark:text-gray-300 font-medium cursor-pointer">
                        <div class="flex items-center">
                            <template x-if="open">
                                <x-icon name="heroicon-o-chevron-right" class="w-4 h-4 mr-2 text-indigo-500 animate-pulse" />
                            </template>
                            <template x-if="!open">
                                <x-icon name="heroicon-o-chevron-right" class="w-4 h-4 mr-2 text-indigo-500 rotate-90" />
                            </template>
                            {{ is_numeric($label) ? 'Item ' . ($label + 1) : $label }}
                        </div>
                        <template x-if="open">
                            <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 text-indigo-500 transition-transform" />
                        </template>
                        <template x-if="!open">
                            <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 text-indigo-500 transition-transform rotate-180" />
                        </template>
                    </div>

                    <div x-show="open" x-collapse class="ml-6">
                        @if(is_array($value))
                            <div class="space-y-3">
                                @foreach($value as $key => $val)
                                    <div x-data="{ subOpen: false }" class="bg-gradient-to-r from-white/40 via-white/30 to-white/20 dark:from-gray-800/40 dark:via-gray-800/30 dark:to-gray-800/20 p-4 rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 border border-indigo-100 dark:border-indigo-900">
                                        @if(is_array($val))
                                            <div @click="subOpen = !subOpen" class="flex items-center justify-between mb-2 cursor-pointer">
                                                <div class="flex items-center">
                                                    <x-icon name="heroicon-o-folder" class="w-4 h-4 mr-2 text-indigo-500 group-hover:rotate-12 transition-transform" />
                                                    <div class="font-medium text-sm text-indigo-600 dark:text-indigo-400">{{ is_numeric($key) ? 'Section ' . ($key + 1) : Str::title($key) }}</div>
                                                </div>
                                                <template x-if="subOpen">
                                                    <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 text-indigo-500 transition-transform" />
                                                </template>
                                                <template x-if="!subOpen">
                                                    <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 text-indigo-500 transition-transform rotate-180" />
                                                </template>
                                            </div>
                                            <div x-show="subOpen" x-collapse class="ml-6 space-y-2">
                                                @foreach($val as $k => $v)
                                                    @if(is_array($v))
                                                        <div x-data="{ deepOpen: false }" class="ml-4 space-y-2">
                                                            <div @click="deepOpen = !deepOpen" class="flex items-center justify-between text-sm cursor-pointer">
                                                                <div class="flex items-center">
                                                                    <x-icon name="heroicon-o-folder" class="w-3 h-3 mr-2 text-indigo-400" />
                                                                    <span class="text-gray-600 dark:text-gray-400 font-medium">{{ is_numeric($k) ? 'Part ' . ($k + 1) : Str::title($k) }}:</span>
                                                                </div>
                                                                <template x-if="deepOpen">
                                                                    <x-icon name="heroicon-o-chevron-down" class="w-3 h-3 text-indigo-400 transition-transform" />
                                                                </template>
                                                                <template x-if="!deepOpen">
                                                                    <x-icon name="heroicon-o-chevron-down" class="w-3 h-3 text-indigo-400 transition-transform rotate-180" />
                                                                </template>
                                                            </div>
                                                            <div x-show="deepOpen" x-collapse>
                                                                @foreach($v as $subk => $subv)
                                                                    @if(is_array($subv))
                                                                        <div x-data="{ ultraDeepOpen: false }" class="ml-4 space-y-2">
                                                                            <div @click="ultraDeepOpen = !ultraDeepOpen" class="flex items-center justify-between text-sm cursor-pointer">
                                                                                <div class="flex items-center">
                                                                                    <x-icon name="heroicon-o-folder" class="w-3 h-3 mr-2 text-indigo-400" />
                                                                                    <span class="text-gray-600 dark:text-gray-400 font-medium">{{ is_numeric($subk) ? 'Group ' . ($subk + 1) : Str::title($subk) }}:</span>
                                                                                </div>
                                                                                <template x-if="ultraDeepOpen">
                                                                                    <x-icon name="heroicon-o-chevron-down" class="w-3 h-3 text-indigo-400 transition-transform" />
                                                                                </template>
                                                                                <template x-if="!ultraDeepOpen">
                                                                                    <x-icon name="heroicon-o-chevron-down" class="w-3 h-3 text-indigo-400 transition-transform rotate-180" />
                                                                                </template>
                                                                            </div>
                                                                            <div x-show="ultraDeepOpen" x-collapse>
                                                                                @foreach($subv as $subsubk => $subsubv)
                                                                                    <div class="flex items-center text-sm group ml-8 hover:bg-white/30 dark:hover:bg-gray-700/30 p-1 rounded-lg transition-colors">
                                                                                        <x-icon name="heroicon-o-chevron-right" class="w-3 h-3 mr-2 text-purple-400 group-hover:text-purple-500 transition-colors" />
                                                                                        <span class="text-gray-600 dark:text-gray-400 font-medium">{{ is_numeric($subsubk) ? 'Item ' . ($subsubk + 1) : Str::title($subsubk) }}:</span>
                                                                                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $subsubv }}</span>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    @else
                                                                        <div class="flex items-center text-sm group ml-4 hover:bg-white/30 dark:hover:bg-gray-700/30 p-1 rounded-lg transition-colors">
                                                                            <x-icon name="heroicon-o-chevron-right" class="w-3 h-3 mr-2 text-purple-400 group-hover:text-purple-500 transition-colors" />
                                                                            <span class="text-gray-600 dark:text-gray-400 font-medium">{{ is_numeric($subk) ? 'Item ' . ($subk + 1) : Str::title($subk) }}:</span>
                                                                            <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $subv }}</span>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="flex items-center text-sm group hover:bg-white/30 dark:hover:bg-gray-700/30 p-1 rounded-lg transition-colors">
                                                            <x-icon name="heroicon-o-chevron-right" class="w-3 h-3 mr-2 text-purple-400 group-hover:text-purple-500 transition-colors" />
                                                            <span class="text-gray-600 dark:text-gray-400 font-medium">{{ is_numeric($k) ? 'Item ' . ($k + 1) : Str::title($k) }}:</span>
                                                            <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $v }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="flex items-center text-sm group hover:bg-white/30 dark:hover:bg-gray-700/30 p-2 rounded-lg transition-colors">
                                                <x-icon name="heroicon-o-document-text" class="w-4 h-4 mr-2 text-purple-500" />
                                                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ is_numeric($key) ? 'Item ' . ($key + 1) : Str::title($key) }}:</span>
                                                <span class="ml-2 text-gray-900 dark:text-gray-100">{{ $val }}</span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                                <x-icon name="heroicon-o-document" class="w-4 h-4 mr-2 text-indigo-500" />
                                <span class="text-gray-900 dark:text-gray-100">{{ $value }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>