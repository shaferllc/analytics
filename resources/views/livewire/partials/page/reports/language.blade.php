<div class="mt-6">
    <h4 class="font-semibold mb-3 flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
        <x-icon name="heroicon-o-language" class="w-5 h-5 text-indigo-400" />
        <span>Language Analysis</span>
        <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
            Language settings and internationalization configuration
        </div>
    </h4>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50">
            <div class="space-y-3">
                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-document-text" class="w-4 h-4" />
                        <span>HTML Lang</span>
                    </span>
                    <span class="text-indigo-200">{{ $report->internationalization['language']['tags']['primary_language'] ?? 'Not specified' }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        The primary language specified in the HTML lang attribute
                    </div>
                </div>

                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-globe-alt" class="w-4 h-4" />
                        <span>Language Switcher</span>
                    </span>
                    <span class="text-indigo-200">
                        @if($report->internationalization['language']['tags']['has_language_switcher'])
                            <x-icon name="heroicon-o-check-circle" class="w-5 h-5 text-emerald-400" />
                        @else
                            <x-icon name="heroicon-o-x-circle" class="w-5 h-5 text-red-400" />
                        @endif
                    </span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        Presence of a language selection mechanism
                    </div>
                </div>

                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-arrows-right-left" class="w-4 h-4" />
                        <span>Alternate Languages</span>
                    </span>
                    <span class="text-indigo-200">{{ count($report->internationalization['language']['tags']['alternate_languages'] ?? []) }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        Number of alternate language versions available
                    </div>
                </div>

                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-flag" class="w-4 h-4" />
                        <span>x-default Present</span>
                    </span>
                    <span class="text-indigo-200">
                        @if($report->internationalization['language']['hreflang']['has_x_default'])
                            <x-icon name="heroicon-o-check-circle" class="w-5 h-5 text-emerald-400" />
                        @else
                            <x-icon name="heroicon-o-x-circle" class="w-5 h-5 text-red-400" />
                        @endif
                    </span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        Presence of default language fallback
                    </div>
                </div>
            </div>
        </div>

        @if(count($report->internationalization['language']['hreflang']['issues']))
            <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50">
                <div>
                    <h5 class="font-semibold mb-2 text-red-400 flex items-center gap-2">
                        <x-icon name="heroicon-o-exclamation-circle" class="w-4 h-4" />
                        <span>Issues</span>
                    </h5>
                    <div class="space-y-2">
                        @foreach($report->internationalization['language']['hreflang']['issues'] as $issue)
                            <div class="text-red-400 text-sm">
                                <div class="flex items-start gap-2">
                                    <x-icon name="heroicon-o-exclamation-circle" class="w-4 h-4 mt-0.5" />
                                    <div>
                                        <p>{{ $issue['description'] }}</p>
                                        @if(isset($issue['hreflang']) || isset($issue['href']))
                                            <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">
                                                @if(isset($issue['hreflang']))Hreflang: {{ $issue['hreflang'] }}@endif
                                                @if(isset($issue['href']))URL: {{ $issue['href'] }}@endif
                                            </code>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if(isset($report->internationalization['language']['hreflang']['recommendations']))
        <div class="mt-4 bg-indigo-900/30 p-4 rounded-lg border border-indigo-800/30">
            <h5 class="font-semibold mb-3 flex items-center gap-2">
                <x-icon name="heroicon-o-light-bulb" class="w-4 h-4 text-amber-400" />
                <span>Recommendations</span>
            </h5>
            <div class="space-y-2">
                @foreach($report->internationalization['language']['hreflang']['recommendations'] as $recommendation)
                    <div class="flex items-start gap-2">
                        <x-icon name="heroicon-o-arrow-right" class="w-4 h-4 mt-0.5 text-indigo-400" />
                        <div class="text-indigo-300 text-sm">
                            {{ $recommendation['text'] }}
                            <a href="{{ $recommendation['w3c_reference'] }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 ml-1">
                                <span>Learn more</span>
                                <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-3 h-3" />
                            </a>
                            @if(isset($recommendation['example']))
                                <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">{{ $recommendation['example'] }}</code>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
