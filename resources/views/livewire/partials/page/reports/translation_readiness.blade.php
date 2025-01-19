<div class="bg-indigo-900/20 p-4 rounded-lg mb-6">
    <h4 class="font-semibold mb-3 flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
        <x-icon name="heroicon-o-language" class="w-4 h-4 text-indigo-400" />
        <span>Translation Readiness Analysis</span>
        <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
            Analysis of content readiness for translation and localization
        </div>
    </h4>

    @if(isset($report->internationalization['translation_readiness']))
        <div class="space-y-4" x-data="{ showMore: false }">
            <div class="text-sm text-indigo-300 flex items-center gap-2">
                <x-icon name="heroicon-o-document-text" class="w-4 h-4" />
                Found {{ $report->internationalization['translation_readiness']['total_translatable_elements'] }} translatable element(s)
            </div>

            {{-- Summary Section - Always Visible --}}
            @if(count($report->internationalization['translation_readiness']['issues']) > 0)
                <div class="bg-indigo-900/30 p-3 rounded">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-icon name="heroicon-o-exclamation-triangle" class="w-4 h-4 text-amber-400" />
                            <span class="text-amber-400">{{ count($report->internationalization['translation_readiness']['issues']) }} issues found</span>
                        </div>
                        <button @click="showMore = !showMore" class="text-indigo-400 hover:text-indigo-300 text-sm flex items-center gap-1">
                            <span x-text="showMore ? 'Show Less' : 'Show More'"></span>
                            <x-icon x-bind:class="showMore ? 'rotate-180' : ''" name="heroicon-o-chevron-down" class="w-4 h-4 transition-transform" />
                        </button>
                    </div>
                </div>
            @endif

            {{-- Detailed Content - Shown when expanded --}}
            <div x-show="showMore" x-collapse>
                {{-- Translatable Content Section --}}
                @if(count($report->internationalization['translation_readiness']['translatable_content']) > 0)
                    <div class="space-y-3 mb-4">
                        <h5 class="font-medium text-sm text-indigo-300">Translatable Content</h5>
                        @foreach($report->internationalization['translation_readiness']['translatable_content'] as $content)
                            <div class="bg-indigo-900/30 p-3 rounded">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium flex items-center gap-2">
                                            <x-icon name="heroicon-o-code-bracket" class="w-4 h-4 text-indigo-400" />
                                            {{ $content['type'] }} in {{ $content['element'] }}
                                        </div>
                                        @if(isset($content['text']))
                                            <div class="text-sm text-indigo-400 mt-1">{{ $content['text'] }}</div>
                                        @endif
                                        @if(isset($content['attributes']))
                                            @foreach($content['attributes'] as $attr => $value)
                                                <div class="text-sm text-indigo-400 mt-1">{{ $attr }}: {{ $value }}</div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="text-sm">
                                        @if($content['has_lang'])
                                            <span class="text-green-400">✓ Has lang attribute</span>
                                        @else
                                            <span class="text-amber-400">Missing lang attribute</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Issues Section --}}
                @if(count($report->internationalization['translation_readiness']['issues']) > 0)
                    <div class="space-y-2 mb-4">
                        <h5 class="font-medium text-sm text-indigo-300">Detected Issues</h5>
                        @foreach($report->internationalization['translation_readiness']['issues'] as $issue)
                            <div class="bg-indigo-900/30 p-3 rounded">
                                <div class="text-sm text-amber-400 flex items-start gap-2">
                                    <x-icon name="heroicon-o-exclamation-circle" class="w-4 h-4 mt-0.5" />
                                    <div>
                                        <div>{{ $issue['recommendation'] }}</div>
                                        <code class="text-xs bg-indigo-950/50 px-2 py-1 rounded mt-1 block">{{ $issue['example'] }}</code>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Recommendations Section --}}
                @if(isset($report->internationalization['translation_readiness']['recommendations']))
                    <div class="space-y-4">
                        {{-- Technical Recommendations --}}
                        <div>
                            <h5 class="font-medium text-sm text-indigo-300 mb-2">Technical Recommendations</h5>
                            <div class="space-y-2">
                                @foreach($report->internationalization['translation_readiness']['recommendations']['technical'] as $rec)
                                    <div class="bg-indigo-900/30 p-3 rounded">
                                        <div class="flex items-start gap-2">
                                            <x-icon name="heroicon-o-check-circle" class="w-4 h-4 mt-0.5 text-indigo-400" />
                                            <div>
                                                <div class="text-sm text-indigo-300">{{ $rec['text'] }}</div>
                                                <code class="text-xs bg-indigo-950/50 px-2 py-1 rounded mt-1 block text-indigo-400">{{ $rec['example'] }}</code>
                                                <a href="{{ $rec['w3c_reference'] }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 mt-1 text-sm">
                                                    <span>Learn more</span>
                                                    <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-3 h-3" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Content Recommendations --}}
                        <div>
                            <h5 class="font-medium text-sm text-indigo-300 mb-2">Content Recommendations</h5>
                            <div class="space-y-2">
                                @foreach($report->internationalization['translation_readiness']['recommendations']['content'] as $rec)
                                    <div class="bg-indigo-900/30 p-3 rounded">
                                        <div class="flex items-start gap-2">
                                            <x-icon name="heroicon-o-check-circle" class="w-4 h-4 mt-0.5 text-indigo-400" />
                                            <div>
                                                <div class="text-sm text-indigo-300">{{ $rec['text'] }}</div>
                                                <code class="text-xs bg-indigo-950/50 px-2 py-1 rounded mt-1 block text-indigo-400">{{ $rec['example'] }}</code>
                                                <a href="{{ $rec['w3c_reference'] }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 mt-1 text-sm">
                                                    <span>Learn more</span>
                                                    <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-3 h-3" />
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="text-indigo-300 flex items-center gap-2">
            <x-icon name="heroicon-o-x-circle" class="w-4 h-4" />
            Translation readiness analysis not available.
        </div>
    @endif
</div>
