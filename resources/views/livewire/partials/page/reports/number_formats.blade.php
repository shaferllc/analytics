<div class="bg-indigo-900/20 p-4 rounded-lg mb-6">
    <h4 class="font-semibold mb-3 flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
        <x-icon name="heroicon-o-calculator" class="w-4 h-4 text-indigo-400" />
        <span>Number Format Analysis</span>
        <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
            Analysis of number formats used across the website content
        </div>
    </h4>

    @if(isset($report->internationalization['number_formats']))
        @if(count($report->internationalization['number_formats']['formats']) > 0)
            <div class="space-y-4">
                <div class="text-sm text-indigo-300 flex items-center gap-2">
                    <x-icon name="heroicon-o-document-text" class="w-4 h-4" />
                    Found {{ $report->internationalization['number_formats']['total_numbers'] }} number format(s)
                </div>

                <div class="space-y-3">
                    @foreach($report->internationalization['number_formats']['formats'] as $format)
                        <div class="bg-indigo-900/30 p-3 rounded">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium flex items-center gap-2">
                                        <x-icon name="heroicon-o-code-bracket" class="w-4 h-4 text-indigo-400" />
                                        {{ $format['type'] }}
                                    </div>
                                    <div class="text-sm text-indigo-400 flex items-center gap-2 mt-1">
                                        <x-icon name="heroicon-o-tag" class="w-3 h-3" />
                                        Found in {{ $format['element'] }}
                                    </div>
                                </div>
                                <div class="text-sm text-indigo-300">{{ $format['value'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(count($report->internationalization['number_formats']['recommendations']) > 0)
                    <div class="mt-4">
                        <h5 class="font-medium mb-2 flex items-center gap-2" x-data="{ tooltip: false }" @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                            <x-icon name="heroicon-o-light-bulb" class="w-4 h-4 text-amber-400" />
                            <span>Recommendations</span>
                            <div x-show="tooltip" class="absolute mt-8 bg-indigo-800 text-xs p-2 rounded shadow-lg max-w-xs">
                                Best practices and suggestions for number format implementation
                            </div>
                        </h5>
                        <ul class="list-disc list-inside space-y-2 text-sm text-indigo-300">
                            @foreach($report->internationalization['number_formats']['recommendations'] as $recommendation)
                                <li class="flex items-start gap-2">
                                    <x-icon name="heroicon-o-check-circle" class="w-4 h-4 mt-0.5 flex-shrink-0" />
                                    <div>
                                        {{ $recommendation['text'] }}
                                        <div class="ml-0 mt-1 text-indigo-400 bg-indigo-950/50 p-2 rounded">
                                            <code>{{ $recommendation['example'] }}</code>
                                        </div>
                                        <a href="{{ $recommendation['w3c_reference'] }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 mt-1">
                                            <span>Learn more</span>
                                            <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-4 h-4" />
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @else
            <div class="text-indigo-300 flex items-center gap-2">
                <x-icon name="heroicon-o-information-circle" class="w-4 h-4" />
                No number formats detected in the content.
            </div>
        @endif
    @else
        <div class="text-indigo-300 flex items-center gap-2">
            <x-icon name="heroicon-o-x-circle" class="w-4 h-4" />
            Number format analysis not available.
        </div>
    @endif
</div>
