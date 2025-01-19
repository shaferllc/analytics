<div class="mt-6">
    <h4 class="font-semibold mb-3 flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
        <x-icon name="heroicon-o-code-bracket" class="w-5 h-5 text-indigo-400" />
        <span>{{ __('Character Encoding') }}</span>
        <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
            {{ __('Character encoding settings and recommendations for proper text handling') }}
        </div>
    </h4>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50 hover:bg-indigo-900/60 transition-colors">
            <div class="space-y-3">
                <div class="flex items-center justify-between" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-document-text" class="w-4 h-4" />
                        <span>{{ __('Charset Meta Tag') }}</span>
                    </span>
                    <span class="text-indigo-200 bg-indigo-950/50 px-3 py-1.5 rounded-full">{{ $report->internationalization['character_encoding']['charset_meta'] }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('The charset meta tag specifies the character encoding for the HTML document') }}
                    </div>
                </div>

                <div class="flex items-center justify-between" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-server" class="w-4 h-4" />
                        <span>{{ __('Content-Type Charset') }}</span>
                    </span>
                    <span class="text-indigo-200 bg-indigo-950/50 px-3 py-1.5 rounded-full">{{ $report->internationalization['character_encoding']['content_type_charset'] ?? 'N/A' }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('Character encoding specified in the HTTP Content-Type header') }}
                    </div>
                </div>

                <div class="flex items-center justify-between" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-code-bracket-square" class="w-4 h-4" />
                        <span>{{ __('XML Encoding') }}</span>
                    </span>
                    <span class="text-indigo-200 bg-indigo-950/50 px-3 py-1.5 rounded-full">{{ $report->internationalization['character_encoding']['xml_encoding'] }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('Character encoding declared in XML declaration if present') }}
                    </div>
                </div>

                <div class="flex items-center justify-between" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-language" class="w-4 h-4" />
                        <span>{{ __('Declared Encoding') }}</span>
                    </span>
                    <span class="text-indigo-200 bg-indigo-950/50 px-3 py-1.5 rounded-full">{{ $report->internationalization['character_encoding']['declared_encoding'] }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('The primary character encoding detected from all declarations') }}
                    </div>
                </div>

                <div class="flex items-center justify-between" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-document-magnifying-glass" class="w-4 h-4" />
                        <span>{{ __('BOM Detected') }}</span>
                    </span>
                    <span class="text-indigo-200">
                        @if($report->internationalization['character_encoding']['bom_detected'])
                            <span class="inline-flex items-center gap-1 text-emerald-400">
                                <x-icon name="heroicon-o-check-circle" class="w-5 h-5" />
                                {{ __('Yes') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-red-400">
                                <x-icon name="heroicon-o-x-circle" class="w-5 h-5" />
                                {{ __('No') }}
                            </span>
                        @endif
                    </span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('Presence of Byte Order Mark (BOM) in the document') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50 hover:bg-indigo-900/60 transition-colors">
            <div class="space-y-4">
                @if(count($report->internationalization['character_encoding']['issues']))
                    <div>
                        <h5 class="font-semibold mb-2 text-red-400 flex items-center gap-2">
                            <x-icon name="heroicon-o-exclamation-circle" class="w-4 h-4" />
                            <span>{{ __('Issues') }}</span>
                        </h5>
                        <div class="space-y-2">
                            @foreach($report->internationalization['character_encoding']['issues'] as $issue)
                                <div class="bg-red-500/10 p-3 rounded-lg border border-red-500/20">
                                    <div class="flex items-start gap-2">
                                        <x-icon name="heroicon-o-exclamation-circle" class="w-4 h-4 mt-0.5 text-red-400" />
                                        <div>
                                            <p class="text-red-300 text-sm">{{ __($issue['description']) }}</p>
                                            @if(isset($issue['example']))
                                                <code class="block mt-2 text-xs bg-red-950/50 p-2 rounded font-mono">{{ $issue['example'] }}</code>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(count($report->internationalization['character_encoding']['warnings']))
                    <div>
                        <h5 class="font-semibold mb-2 text-amber-400 flex items-center gap-2">
                            <x-icon name="heroicon-o-exclamation-triangle" class="w-4 h-4" />
                            <span>{{ __('Warnings') }}</span>
                        </h5>
                        <div class="space-y-2">
                            @foreach($report->internationalization['character_encoding']['warnings'] as $warning)
                                <div class="bg-amber-500/10 p-3 rounded-lg border border-amber-500/20">
                                    <div class="flex items-start gap-2">
                                        <x-icon name="heroicon-o-exclamation-triangle" class="w-4 h-4 mt-0.5 text-amber-400" />
                                        <div>
                                            <p class="text-amber-300 text-sm">{{ __($warning['description']) }}</p>
                                            @if(isset($warning['example']))
                                                <code class="block mt-2 text-xs bg-amber-950/50 p-2 rounded font-mono">{{ $warning['example'] }}</code>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(isset($report->internationalization['character_encoding']['recommendations']))
        <div class="mt-4 bg-indigo-900/30 p-4 rounded-lg border border-indigo-800/30 hover:bg-indigo-900/40 transition-colors">
            <h5 class="font-semibold mb-3 flex items-center gap-2">
                <x-icon name="heroicon-o-light-bulb" class="w-4 h-4 text-amber-400" />
                <span>{{ __('Recommendations') }}</span>
            </h5>
            <div class="space-y-2">
                @foreach($report->internationalization['character_encoding']['recommendations'] as $recommendation)
                    <div class="flex items-start gap-2">
                        <x-icon name="heroicon-o-arrow-right" class="w-4 h-4 mt-0.5 text-indigo-400" />
                        <div class="text-indigo-300 text-sm">
                            {{ __($recommendation['text']) }}
                            <a href="{{ $recommendation['reference'] }}" target="_blank" rel="noopener" class="text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 ml-1 transition-colors">
                                <span>{{ __('Learn more') }}</span>
                                <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-3 h-3" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>