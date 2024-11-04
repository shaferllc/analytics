@dump($opengraphAnalysis)
<div class="space-y-6">
    <div class="flex items-center justify-between text-lg font-bold text-gray-800 dark:text-gray-100 mt-4 bg-gradient-to-r from-indigo-100 via-blue-100 to-purple-100 dark:from-indigo-900 dark:via-blue-900 dark:to-purple-900 p-6 rounded-xl shadow-lg transform hover:scale-[1.02] transition-all duration-300">
        <div class="flex flex-col lg:flex-row justify-between gap-6 p-8 w-full bg-gradient-to-r from-indigo-100 via-blue-100 to-purple-100 dark:from-indigo-900 dark:via-blue-900 dark:to-purple-900 rounded-xl shadow-lg transform hover:scale-[1.02] transition-all duration-300">
            <div class="flex-grow space-y-4">
                <div class="flex items-center">
                    <x-icon name="heroicon-o-globe-alt" class="w-10 h-10 mr-4 text-indigo-500 animate-pulse" />
                    <h2 class="text-3xl font-bold text-gray-800 dark:text-gray-100">
                        {{ __('OpenGraph Analysis') }}
                    </h2>
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-400 space-y-4">
                    <div class="font-medium">
                        Last fetched: {{ $opengraphAnalysis['last_fetched'] ?? 'Never' }}
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if(isset($opengraphAnalysis['response_time']))
                            <div class="flex items-center bg-white/30 dark:bg-gray-800/30 rounded-lg p-2">
                                <x-icon name="heroicon-o-clock" class="w-5 h-5 mr-2 text-indigo-500" />
                                <span>{{ number_format($opengraphAnalysis['response_time'] * 1000, 0) }}ms</span>
                            </div>
                        @endif
                        @if(isset($opengraphAnalysis['content_type']))
                            <div class="flex items-center bg-white/30 dark:bg-gray-800/30 rounded-lg p-2">
                                <x-icon name="heroicon-o-document" class="w-5 h-5 mr-2 text-indigo-500" />
                                <span>{{ $opengraphAnalysis['content_type'] }}</span>
                            </div>
                        @endif
                        @if(isset($opengraphAnalysis['content_length']))
                            <div class="flex items-center bg-white/30 dark:bg-gray-800/30 rounded-lg p-2">
                                <x-icon name="heroicon-o-chart-bar" class="w-5 h-5 mr-2 text-indigo-500" />
                                <span>{{ number_format(intval($opengraphAnalysis['content_length']) / 1024, 2) }}KB</span>
                            </div>
                        @endif
                        @if(isset($opengraphAnalysis['language']))
                            <div class="flex items-center bg-white/30 dark:bg-gray-800/30 rounded-lg p-2">
                                <x-icon name="heroicon-o-language" class="w-5 h-5 mr-2 text-indigo-500" />
                                <span>{{ strtoupper($opengraphAnalysis['language']) }}</span>
                            </div>
                        @endif
                    </div>

                    @if(isset($opengraphAnalysis['performance_metrics']))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 border-t border-gray-200 dark:border-gray-700 pt-4">
                            <div class="flex items-center bg-white/30 dark:bg-gray-800/30 rounded-lg p-2">
                                <x-icon name="heroicon-o-clock" class="w-5 h-5 mr-2 text-purple-500" />
                                <span>Total: {{ number_format($opengraphAnalysis['performance_metrics']['total_time'] * 1000, 0) }}ms</span>
                            </div>
                            <div class="flex items-center bg-white/30 dark:bg-gray-800/30 rounded-lg p-2">
                                <x-icon name="heroicon-o-arrow-path" class="w-5 h-5 mr-2 text-purple-500" />
                                <span>TTFB: {{ number_format($opengraphAnalysis['performance_metrics']['ttfb'] * 1000, 0) }}ms</span>
                            </div>
                            <div class="flex items-center bg-white/30 dark:bg-gray-800/30 rounded-lg p-2">
                                <x-icon name="heroicon-o-server" class="w-5 h-5 mr-2 text-purple-500" />
                                <span>DNS: {{ number_format($opengraphAnalysis['performance_metrics']['dns_lookup'] * 1000, 0) }}ms</span>
                            </div>
                            <div class="flex items-center bg-white/30 dark:bg-gray-800/30 rounded-lg p-2">
                                <x-icon name="heroicon-o-signal" class="w-5 h-5 mr-2 text-purple-500" />
                                <span>TCP: {{ number_format($opengraphAnalysis['performance_metrics']['tcp_connect'] * 1000, 0) }}ms</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center justify-center lg:border-l lg:border-gray-200 dark:lg:border-gray-700 lg:pl-8">
                <div class="text-center">
                    <div class="text-lg text-gray-600 dark:text-gray-400 mb-2">Score</div>
                    <div class="text-5xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 to-purple-500">
                        {{ $opengraphAnalysis['score'] ?? 0 }}/100
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(empty($opengraphAnalysis))
        <x-analytics::no-results />
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @include('analytics::livewire.domain.opengraph.basic-meta')

            @include('analytics::livewire.domain.opengraph.facebook-meta')

            @include('analytics::livewire.domain.opengraph.twitter-meta')

            @include('analytics::livewire.domain.opengraph.opengraph')

            @include('analytics::livewire.domain.opengraph.score')

            @if(!empty($opengraphAnalysis['warnings']))
                <div class="bg-gradient-to-br from-yellow-100 via-orange-100 to-red-100 dark:from-yellow-900 dark:via-orange-900 dark:to-red-900 rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition-all duration-300 border border-yellow-200 dark:border-yellow-800">
                    <h3 class="text-2xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-yellow-600 to-orange-600 dark:from-yellow-300 dark:to-orange-300 flex items-center">
                        <x-icon name="heroicon-o-exclamation-triangle" class="w-7 h-7 mr-3 text-yellow-500 animate-pulse" />
                        Warnings
                    </h3>
                    <div class="space-y-4">
                        @foreach($opengraphAnalysis['warnings'] as $warning)
                            <div class="flex items-center p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                                <x-icon name="heroicon-o-exclamation-circle" class="w-5 h-5 mr-3 text-yellow-500" />
                                <span class="text-gray-700 dark:text-gray-300">{{ $warning }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($opengraphAnalysis['accessibility']))
                <div class="bg-gradient-to-br from-blue-100 via-indigo-100 to-purple-100 dark:from-blue-900 dark:via-indigo-900 dark:to-purple-900 rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition-all duration-300 border border-blue-200 dark:border-blue-800">
                    <h3 class="text-2xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-300 dark:to-indigo-300 flex items-center">
                        <x-icon name="heroicon-o-eye" class="w-7 h-7 mr-3 text-blue-500 animate-pulse" />
                        Accessibility Issues
                    </h3>
                    <div class="space-y-4">
                        @foreach($opengraphAnalysis['accessibility'] as $issue)
                            <div class="flex items-center p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                                <x-icon name="heroicon-o-exclamation-circle" class="w-5 h-5 mr-3 text-blue-500" />
                                <span class="text-gray-700 dark:text-gray-300">{{ $issue }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($opengraphAnalysis['metas']))
                <div class="bg-gradient-to-br from-emerald-100 via-teal-100 to-cyan-100 dark:from-emerald-900 dark:via-teal-900 dark:to-cyan-900 rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition-all duration-300 border border-emerald-200 dark:border-emerald-800">
                    <h3 class="text-2xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-300 dark:to-teal-300 flex items-center">
                        <x-icon name="heroicon-o-document-text" class="w-7 h-7 mr-3 text-emerald-500 animate-pulse" />
                        Meta Tags
                    </h3>
                    <div class="space-y-4">
                        @foreach($opengraphAnalysis['metas'] as $name => $content)
                            <div class="flex items-center p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                                <x-icon name="heroicon-o-tag" class="w-5 h-5 mr-3 text-emerald-500" />
                                <div class="flex flex-col">
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ Str::title(str_replace(['_', '-'], ' ', $name)) }}</span>
                                    @if(is_array($content))
                                        <span class="text-gray-700 dark:text-gray-300 text-sm ml-4">
                                            @php
                                                $flattened = [];
                                                array_walk_recursive($content, function($item) use (&$flattened) {
                                                    $flattened[] = $item;
                                                });
                                                echo implode(', ', array_filter($flattened));
                                            @endphp
                                        </span>
                                    @else
                                        <span class="text-gray-700 dark:text-gray-300 text-sm ml-4">{{ $content }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(!empty($opengraphAnalysis['ld_json']))
                <div class="bg-gradient-to-br from-amber-100 via-orange-100 to-rose-100 dark:from-amber-900 dark:via-orange-900 dark:to-rose-900 rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition-all duration-300 border border-amber-200 dark:border-amber-800">
                    <h3 class="text-2xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-amber-600 to-orange-600 dark:from-amber-300 dark:to-orange-300 flex items-center">
                        <x-icon name="heroicon-o-code-bracket" class="w-7 h-7 mr-3 text-amber-500 animate-pulse" />
                        Structured Data
                    </h3>
                    <div class="space-y-4">
                        @foreach($opengraphAnalysis['ld_json'] as $key => $value)
                            <div class="flex flex-col p-3 bg-white/30 dark:bg-gray-800/30 rounded-lg hover:bg-white/50 dark:hover:bg-gray-800/50 transition-all duration-300">
                                <div class="flex items-center">
                                    <x-icon name="heroicon-o-hashtag" class="w-5 h-5 mr-3 text-amber-500" />
                                    <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ Str::title(str_replace(['_', '-'], ' ', $key)) }}</span>
                                </div>
                                @if(is_array($value))
                                    <div class="mt-2 ml-8 space-y-2">
                                        @foreach($value as $subKey => $subValue)
                                            <div class="flex items-start">
                                                <span class="text-gray-700 dark:text-gray-300 font-medium mr-2">{{ Str::title(str_replace(['_', '-'], ' ', $subKey)) }}:</span>
                                                @if(is_array($subValue))
                                                    <div class="flex flex-col space-y-1">
                                                        @foreach($subValue as $item)
                                                            @if(is_array($item))
                                                                <div class="ml-2 p-2 bg-white/50 dark:bg-gray-700/50 rounded">
                                                                    @foreach($item as $itemKey => $itemValue)
                                                                        <div class="text-sm">
                                                                            <span class="text-gray-600 dark:text-gray-400">{{ Str::title(str_replace(['_', '-'], ' ', $itemKey)) }}:</span>
                                                                            <span class="text-gray-800 dark:text-gray-200">{{ is_string($itemValue) ? $itemValue : json_encode($itemValue) }}</span>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <span class="text-gray-600 dark:text-gray-400">{{ $item }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-gray-600 dark:text-gray-400">{{ $subValue }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-700 dark:text-gray-300 ml-8 mt-1">{{ $value }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
