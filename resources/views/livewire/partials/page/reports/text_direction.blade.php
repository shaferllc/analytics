<div class="mt-6">
    <h4 class="font-semibold mb-3 flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
        <x-icon name="heroicon-o-arrow-right" class="w-5 h-5 text-indigo-400" />
        <span>Text Direction</span>
        <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
            Analysis of text direction and RTL support implementation
        </div>
    </h4>

    <div class="grid grid-cols-2 gap-4">
        <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50">
            <div class="space-y-3">
                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-document-text" class="w-4 h-4" />
                        <span>Document Direction</span>
                    </span>
                    <span class="text-indigo-200">{{ strtoupper($report->internationalization['text_direction']['document_direction']) }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        The primary text direction of the HTML document
                        <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">&lt;html dir="ltr"&gt;</code>
                    </div>
                </div>

                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-language" class="w-4 h-4" />
                        <span>RTL Support</span>
                    </span>
                    <span class="text-indigo-200">
                        @if($report->internationalization['text_direction']['has_rtl_support'])
                            <x-icon name="heroicon-o-check-circle" class="w-5 h-5 text-emerald-400" />
                        @else
                            <x-icon name="heroicon-o-x-circle" class="w-5 h-5 text-red-400" />
                        @endif
                    </span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        Whether the page has proper RTL (Right-to-Left) language support
                        <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">&lt;html dir="rtl" lang="ar"&gt;</code>
                    </div>
                </div>

                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-arrows-right-left" class="w-4 h-4" />
                        <span>Mixed Directions</span>
                    </span>
                    <span class="text-indigo-200">
                        @if($report->internationalization['text_direction']['has_mixed_directions'])
                            <x-icon name="heroicon-o-check-circle" class="w-5 h-5 text-emerald-400" />
                        @else
                            <x-icon name="heroicon-o-x-circle" class="w-5 h-5 text-red-400" />
                        @endif
                    </span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        Presence of content with different text directions on the page
                        <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">&lt;p dir="rtl"&gt;Arabic text&lt;/p&gt;
&lt;p dir="ltr"&gt;English text&lt;/p&gt;</code>
                    </div>
                </div>

                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-document-duplicate" class="w-4 h-4" />
                        <span>RTL Elements</span>
                    </span>
                    <span class="text-indigo-200">{{ $report->internationalization['text_direction']['total_rtl_elements'] }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        Number of elements with RTL text direction
                        <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">&lt;div dir="rtl"&gt;محتوى عربي&lt;/div&gt;</code>
                    </div>
                </div>

                <div class="flex items-center justify-between group" x-data="{ hint: false }" @mouseenter="hint = true" @mouseleave="hint = false">
                    <span class="text-indigo-300 flex items-center gap-2">
                        <x-icon name="heroicon-o-arrows-up-down" class="w-4 h-4" />
                        <span>Bidirectional Elements</span>
                    </span>
                    <span class="text-indigo-200">{{ $report->internationalization['text_direction']['total_bidi_elements'] }}</span>
                    <div x-show="hint" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        Number of elements with bidirectional text content
                        <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">&lt;p&gt;English text مع نص عربي&lt;/p&gt;</code>
                    </div>
                </div>
            </div>
        </div>

   
        <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50">
            <div class="space-y-4">
                @if(count($report->internationalization['text_direction']['rtl_stylesheets']))
                    <div>
                        <h5 class="font-semibold mb-2 text-indigo-200 flex items-center gap-2">
                            <x-icon name="heroicon-o-document-text" class="w-4 h-4" />
                            <span>RTL Stylesheets</span>
                        </h5>
                        <div class="space-y-2">
                            @foreach($report->internationalization['text_direction']['rtl_stylesheets'] as $stylesheet)
                                <div class="text-indigo-300 text-sm">
                                    <div class="flex items-start gap-2">
                                        <x-icon name="heroicon-o-document" class="w-4 h-4 mt-0.5" />
                                        <code class="block text-xs bg-indigo-950/50 p-2 rounded">{{ $stylesheet }}</code>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div>
                        <h5 class="font-semibold mb-2 text-indigo-200 flex items-center gap-2">
                            <x-icon name="heroicon-o-document-text" class="w-4 h-4" />
                            <span>RTL Stylesheets</span>
                        </h5>
                        <p class="text-indigo-300 text-sm italic">No RTL stylesheets detected</p>
                        <code class="block mt-2 text-xs bg-indigo-950/50 p-2 rounded">&lt;link rel="stylesheet" href="styles-rtl.css"&gt;</code>
                    </div>
                @endif

                @if(count($report->internationalization['text_direction']['bidirectional_elements']))
                    <div>
                        <h5 class="font-semibold mb-2 text-indigo-200 flex items-center gap-2">
                            <x-icon name="heroicon-o-code-bracket" class="w-4 h-4" />
                            <span>Bidirectional Content</span>
                        </h5>
                        <div class="space-y-2">
                            @foreach($report->internationalization['text_direction']['bidirectional_elements'] as $element)
                                <div class="text-indigo-300 text-sm">
                                    <div class="flex items-start gap-2">
                                        <x-icon name="heroicon-o-code-bracket" class="w-4 h-4 mt-0.5" />
                                        <div>
                                            <p class="text-xs text-indigo-400">{{ $element['element'] }}</p>
                                            <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">{{ $element['text'] }}</code>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div>
                        <h5 class="font-semibold mb-2 text-indigo-200 flex items-center gap-2">
                            <x-icon name="heroicon-o-code-bracket" class="w-4 h-4" />
                            <span>Bidirectional Content</span>
                        </h5>
                        <p class="text-indigo-300 text-sm italic">No bidirectional content detected</p>
                        <code class="block mt-2 text-xs bg-indigo-950/50 p-2 rounded">&lt;p&gt;Hello مرحبا&lt;/p&gt;</code>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(isset($report->internationalization['text_direction']['recommendations']))
        <div class="mt-4 bg-indigo-900/30 p-4 rounded-lg border border-indigo-800/30">
            <h5 class="font-semibold mb-3 flex items-center gap-2">
                <x-icon name="heroicon-o-light-bulb" class="w-4 h-4 text-amber-400" />
                <span>Recommendations</span>
            </h5>
            <div class="space-y-2">
                @foreach($report->internationalization['text_direction']['recommendations'] as $recommendation)
                    <div class="flex items-start gap-2">
                        <x-icon name="heroicon-o-arrow-right" class="w-4 h-4 mt-0.5 text-indigo-400" />
                        <div class="text-indigo-300 text-sm">
                            {{ $recommendation['text'] }}
                            <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">{{ $recommendation['example'] }}</code>
                            <a href="{{ $recommendation['reference'] }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 ml-1">
                                <span>Learn more</span>
                                <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-3 h-3" />
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
