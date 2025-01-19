<div class="bg-indigo-950 rounded-lg border border-indigo-900/50">
    <div class="p-4">
        <div class="flex items-center justify-between">
            <h4 class="font-semibold text-indigo-100">Content Localization</h4>
            <div class="flex items-center gap-2">
                @if($report->internationalization['content_localization']['passed'])
                    <span class="px-2 py-1 text-xs font-medium bg-green-500/20 text-green-400 rounded-full">Passed</span>
                @else
                    <span class="px-2 py-1 text-xs font-medium bg-amber-500/20 text-amber-400 rounded-full">Review Needed</span>
                @endif
                <span class="px-2 py-1 text-xs font-medium bg-indigo-900/50 text-indigo-300 rounded-full">{{ $report->internationalization['content_localization']['importance'] }}</span>
            </div>
        </div>

        <div class="mt-4 space-y-6">
            @if(count($report->internationalization['content_localization']['elements']))
                <div class="space-y-4">
                    @foreach($report->internationalization['content_localization']['elements'] as $element)
                        <div class="bg-indigo-900/30 p-4 rounded-lg border border-indigo-800/30">
                            <div class="flex items-start gap-2">
                                <x-icon name="heroicon-o-language" class="w-4 h-4 mt-1 text-indigo-400" />
                                <div class="flex-1">
                                    <h5 class="font-medium text-indigo-200 capitalize">{{ str_replace('_', ' ', $element['type']) }}</h5>
                                    <div class="mt-2 space-y-2">
                                        <div class="text-indigo-300 text-sm">
                                            <p class="text-xs text-indigo-400">Current Content:</p>
                                            <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">{{ $element['content'] }}</code>
                                        </div>
                                        <div class="text-indigo-300 text-sm">
                                            <p class="text-xs text-indigo-400">Example:</p>
                                            <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">{{ $element['example'] }}</code>
                                        </div>
                                        <div class="flex items-center gap-1 text-sm">
                                            <span class="text-indigo-400">Reference:</span>
                                            <a href="{{ $element['reference'] }}" target="_blank" class="text-indigo-300 hover:text-indigo-200 inline-flex items-center gap-1">
                                                <span>Documentation</span>
                                                <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-3 h-3" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-indigo-300 text-sm italic">
                    No localizable content detected
                </div>
            @endif

            @if(isset($report->internationalization['content_localization']['details']))
                <div class="bg-indigo-900/30 p-4 rounded-lg border border-indigo-800/30">
                    <h5 class="font-semibold mb-3 flex items-center gap-2">
                        <x-icon name="heroicon-o-light-bulb" class="w-4 h-4 text-amber-400" />
                        <span>Recommendations</span>
                    </h5>
                    <div class="space-y-3">
                        @foreach($report->internationalization['content_localization']['details'] as $detail)
                            @if(is_array($detail))
                                <div class="flex items-start gap-2">
                                    <x-icon name="heroicon-o-arrow-right" class="w-4 h-4 mt-0.5 text-indigo-400" />
                                    <div class="text-indigo-300 text-sm">
                                        {{ $detail['text'] }}
                                        <code class="block mt-1 text-xs bg-indigo-950/50 p-2 rounded">{{ $detail['example'] }}</code>
                                        <a href="{{ $detail['reference'] }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1 ml-1">
                                            <span>Learn more</span>
                                            <x-icon name="heroicon-o-arrow-top-right-on-square" class="w-3 h-3" />
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start gap-2">
                                    <x-icon name="heroicon-o-information-circle" class="w-4 h-4 mt-0.5 text-indigo-400" />
                                    <span class="text-indigo-300 text-sm">{{ $detail }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
