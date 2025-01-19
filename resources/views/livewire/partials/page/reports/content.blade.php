<div class="mt-6">
    <h4 class="font-semibold mb-3 flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
        <x-icon name="heroicon-o-document-text" class="w-5 h-5 text-indigo-400" />
        <span>Content Localization</span>
        <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
            Analysis of content translation and localization patterns
        </div>
    </h4>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50">
            <div class="space-y-3">
                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-language" class="w-4 h-4" />
                        <span>Localized Elements</span>
                    </span>
                    <span class="text-indigo-200">{{ $report->internationalization['content']['total_localized'] }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        Total number of elements marked for translation
                    </div>
                </div>

                @if(count($report->internationalization['content']['localized_elements']))
                    <div class="space-y-2 mt-4">
                        @foreach($report->internationalization['content']['localized_elements'] as $element)
                            <div class="text-sm text-indigo-300">
                                <div class="flex items-start gap-2">
                                    <x-icon name="heroicon-o-code-bracket" class="w-4 h-4 mt-0.5" />
                                    <div>
                                        <p class="text-indigo-200">{{ $element['text'] }}</p>
                                        <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">{{ $element['example'] }}</code>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if(count($report->internationalization['content']['potential_issues']))
            <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50">
                <h5 class="font-semibold mb-2 text-red-400 flex items-center gap-2">
                    <x-icon name="heroicon-o-exclamation-circle" class="w-4 h-4" />
                    <span>Potential Issues</span>
                </h5>
                <div class="space-y-2">
                    @foreach($report->internationalization['content']['potential_issues'] as $issue)
                        <div class="text-red-400 text-sm">
                            <div class="flex items-start gap-2">
                                <x-icon name="heroicon-o-exclamation-circle" class="w-4 h-4 mt-0.5" />
                                <div>
                                    <p>{{ $issue['description'] }}</p>
                                    <p class="text-xs mt-1">{{ $issue['text'] }}</p>
                                    @if(isset($issue['example']))
                                        <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">{{ $issue['example'] }}</code>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @if(isset($report->internationalization['content']['recommendations']))
        <div class="mt-4 bg-indigo-900/30 p-4 rounded-lg border border-indigo-800/30">
            <h5 class="font-semibold mb-3 flex items-center gap-2">
                <x-icon name="heroicon-o-light-bulb" class="w-4 h-4 text-amber-400" />
                <span>Recommendations</span>
            </h5>
            <div class="space-y-2">
                @foreach($report->internationalization['content']['recommendations'] as $recommendation)
                    <div class="flex items-start gap-2">
                        <x-icon name="heroicon-o-arrow-right" class="w-4 h-4 mt-0.5 text-indigo-400" />
                        <div class="text-indigo-300 text-sm">{{ $recommendation }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
