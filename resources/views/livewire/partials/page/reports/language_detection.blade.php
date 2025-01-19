<div class="grid grid-cols-2 gap-4">
        <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50 hover:bg-indigo-900/60 transition-colors">
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                    <x-icon name="heroicon-o-language" class="w-4 h-4 text-indigo-400" />
                    <span>{{ __('Primary Language') }}</span>
                    <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('The main language detected in the page content') }}
                    </div>
                </h4>
                <span class="text-indigo-300 bg-indigo-950/50 px-3 py-1.5 rounded-full">{{ $report->internationalization['language_detection']['primary_language'] }}</span>
            </div>
            <div class="flex items-center justify-between mb-2">
                <h4 class="font-semibold flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                    <x-icon name="heroicon-o-document-text" class="w-4 h-4 text-indigo-400" />
                    <span>{{ __('Language Elements') }}</span>
                    <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('Number of HTML elements with language-specific attributes (lang, dir, etc.)') }}
                    </div>
                </h4>
                <span class="text-indigo-300 bg-indigo-950/50 px-3 py-1.5 rounded-full">{{ count($report->internationalization['language_detection']['language_elements']) }}</span>
            </div>
            <div class="flex items-center justify-between">
                <h4 class="font-semibold flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                    <x-icon name="heroicon-o-document-magnifying-glass" class="w-4 h-4 text-indigo-400" />
                    <span>{{ __('Text Elements Analyzed') }}</span>
                    <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('Total number of text elements scanned for language detection') }}
                    </div>
                </h4>
                <span class="text-indigo-300 bg-indigo-950/50 px-3 py-1.5 rounded-full">{{ $report->internationalization['language_detection']['text_elements_analyzed'] }}</span>
            </div>
        </div>

        <div class="bg-indigo-900/50 p-4 rounded-lg border border-indigo-800/50 hover:bg-indigo-900/60 transition-colors">
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                    <x-icon name="heroicon-o-arrows-right-left" class="w-4 h-4 text-indigo-400" />
                    <span>{{ __('BiDi Support') }}</span>
                    <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('Support for bi-directional text (right-to-left and left-to-right languages)') }}
                    </div>
                </h4>
                <span class="text-indigo-300">
                    @if($report->internationalization['language_detection']['bidi_support'])
                        <span class="inline-flex items-center gap-1 text-emerald-400">
                            <x-icon name="heroicon-o-check-circle" class="w-5 h-5" />
                            {{ __('Supported') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-red-400">
                            <x-icon name="heroicon-o-x-circle" class="w-5 h-5" />
                            {{ __('Not Supported') }}
                        </span>
                    @endif
                </span>
            </div>
            <div class="flex items-center justify-between mb-3">
                <h4 class="font-semibold flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                    <x-icon name="heroicon-o-code-bracket" class="w-4 h-4 text-indigo-400" />
                    <span>{{ __('Meta Lang Support') }}</span>
                    <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('Presence of language meta tags in the HTML head') }}
                    </div>
                </h4>
                <span class="text-indigo-300">
                    @if($report->internationalization['language_detection']['meta_lang_support'])
                        <span class="inline-flex items-center gap-1 text-emerald-400">
                            <x-icon name="heroicon-o-check-circle" class="w-5 h-5" />
                            {{ __('Present') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-red-400">
                            <x-icon name="heroicon-o-x-circle" class="w-5 h-5" />
                            {{ __('Missing') }}
                        </span>
                    @endif
                </span>
            </div>
            <div class="flex items-center justify-between">
                <h4 class="font-semibold flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                    <x-icon name="heroicon-o-share" class="w-4 h-4 text-indigo-400" />
                    <span>{{ __('OG Locale Support') }}</span>
                    <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                        {{ __('Presence of Open Graph locale meta tags for social media sharing') }}
                    </div>
                </h4>
                <span class="text-indigo-300">
                    @if($report->internationalization['language_detection']['og_locale_support'])
                        <span class="inline-flex items-center gap-1 text-emerald-400">
                            <x-icon name="heroicon-o-check-circle" class="w-5 h-5" />
                            {{ __('Present') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-red-400">
                            <x-icon name="heroicon-o-x-circle" class="w-5 h-5" />
                            {{ __('Missing') }}
                        </span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    @if(isset($report->internationalization['language_detection']['issues']))
        <div class="mt-6">
            <h4 class="font-semibold mb-3 flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                <x-icon name="heroicon-o-exclamation-triangle" class="w-5 h-5 text-amber-400" />
                <span>{{ __('Language Issues') }}</span>
                <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                    {{ __('Problems detected with language implementation and recommendations for fixes') }}
                </div>
            </h4>
            <div class="space-y-3">
                @foreach($report->internationalization['language_detection']['issues'] as $issue)
                    <div class="bg-indigo-900/30 p-4 rounded-lg border border-indigo-800/30 hover:bg-indigo-900/40 transition-colors">
                        <div class="font-semibold text-indigo-200 flex items-center gap-2">
                            <x-icon name="heroicon-o-exclamation-circle" class="w-4 h-4 text-amber-400" />
                            <span>{{ __(ucfirst(str_replace('_', ' ', $issue['type']))) }}</span>
                        </div>
                        <div class="text-indigo-300 mt-2 ml-6">{{ __($issue['recommendation']) }}</div>
                        @if(Arr::has($issue, 'example'))
                            <div class="text-indigo-400 mt-2 ml-6 p-2 bg-indigo-950/50 rounded font-mono">
                                <code class="text-sm">{{ Arr::get($issue, 'example') }}</code>
                            </div>
                        @endif
                        <a href="{{ $issue['reference'] }}" target="_blank" rel="noopener" class="text-indigo-400 hover:text-indigo-300 text-sm mt-2 ml-6 inline-flex items-center gap-1 transition-colors">
                            <span>{{ __('Learn more') }}</span>
                            <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif