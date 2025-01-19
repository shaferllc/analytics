@php
    $viewportData = $this->getViewportData()['data'];
    $firstViewportData =  $this->getViewportData()['first'];
    $lastViewportData = $this->getViewportData()['last'];
    $aggregates = $this->getViewportData()['aggregates'];

@endphp
<div x-show="activeTab === 'viewport'">
    <div class="flex items-center gap-3 mb-6 text-sm text-indigo-200 bg-indigo-900/20 p-3 rounded-lg">
        <x-icon name="heroicon-o-rectangle-stack" class="w-5 h-5 text-indigo-400 flex-shrink-0" />
        <div>
            <span class="font-medium text-lg">{{ __('Viewport analytics and screen size metrics') }}</span>
            <span class="text-xs text-indigo-300 block mt-0.5">({{ __('Screen sizes, orientations and zoom levels') }})</span>
        </div>
    </div>
    <div class="mb-6 p-4 bg-indigo-900/30 border border-indigo-800 rounded-lg">
        <h3 class="text-lg font-medium text-indigo-100 mb-2">{{ __('Viewport Analytics') }}</h3>
        <p class="text-indigo-300">{{ __('Track how users view your site across different screen sizes, orientations and zoom levels. This helps ensure your site works well across all devices.') }}</p>
        
        <div class="mt-4 flex items-start gap-8">
            <div class="flex-1 flex items-center gap-4">
                <div class="w-32 h-24 relative bg-indigo-800/50 border border-indigo-700 rounded">
                    <div class="absolute inset-1 border-2 border-dashed border-indigo-500/50"></div>
                    <div class="absolute bottom-1 right-1">
                        <x-icon name="heroicon-o-arrows-pointing-out" class="w-4 h-4 text-indigo-400" />
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-sm text-indigo-200">
                        <span class="text-indigo-400">{{ __('Width') }}:</span> {{ __('Browser width in pixels') }}
                    </p>
                    <p class="text-sm text-indigo-200">
                        <span class="text-indigo-400">{{ __('Height') }}:</span> {{ __('Browser height in pixels') }}
                    </p>
                    <p class="text-sm text-indigo-200">
                        <span class="text-indigo-400">{{ __('Orientation') }}:</span> {{ __('Portrait or landscape view') }}
                    </p>
                </div>
            </div>
            <div class="flex-1">
                <ul class="space-y-2 text-sm text-indigo-200">
                    <li class="flex items-center gap-2">
                        <x-icon name="heroicon-o-magnifying-glass" class="w-4 h-4 text-indigo-400" />
                        {{ __('Zoom level shows how much users scale content') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="heroicon-o-arrows-up-down" class="w-4 h-4 text-indigo-400" />
                        {{ __('Scroll area indicates total content size') }}
                    </li>
                    <li class="flex items-center gap-2">
                        <x-icon name="heroicon-o-computer-desktop" class="w-4 h-4 text-indigo-400" />
                        {{ __('Browser details help debug platform-specific issues') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-6">
        @foreach($viewportData as $data)
            @foreach($data->value as $item)
                <div class="p-4 bg-indigo-900/50 border border-indigo-700 rounded-lg">
                    <div class="flex items-start justify-between">
                        <div>
                            <h4 class="font-medium text-indigo-100">
                                @if($item['event'] === 'viewport_change')
                                    {{ __('Viewport Change') }}
                                @else
                                    {{ __('Viewport Size') }}
                                @endif
                            </h4>
                            
                            <div class="mt-2 space-y-1">
                                <div class="flex items-center gap-2 text-sm text-indigo-300">
                                    <x-icon name="heroicon-o-arrows-pointing-out" class="w-4 h-4 text-indigo-400"/>
                                    <span>{{ $item['width'] }}x{{ $item['height'] }}px</span>
                                </div>

                                <div class="flex items-center gap-2 text-sm text-indigo-300">
                                    <x-icon name="heroicon-o-device-phone-mobile" class="w-4 h-4 text-indigo-400"/>
                                    <span>{{ $item['orientation'] }}</span>
                                </div>

                                @if(isset($data['value']['zoom']))
                                <div class="flex items-center gap-2 text-sm text-indigo-300">
                                    <x-icon name="heroicon-o-magnifying-glass" class="w-4 h-4 text-indigo-400"/>
                                    <span>{{ $item['zoom'] }}% zoom</span>
                                </div>
                                @endif

                                @if(isset($item['scrollWidth']) && isset($item['scrollHeight']))
                                <div class="flex items-center gap-2 text-sm text-indigo-300">
                                    <x-icon name="heroicon-o-arrows-up-down" class="w-4 h-4 text-indigo-400"/>
                                    <span>{{ $item['scrollWidth'] }}x{{ $item['scrollHeight'] }}px scroll area</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="relative w-20 h-20 bg-indigo-800/30 border border-indigo-600 rounded">
                            <div class="absolute inset-2">
                                <div class="w-full h-full border-2 border-dashed border-indigo-500/50 rounded-sm"
                                    style="transform: scale({{ $item['width'] / max($item['width'], $item['height']) }}, {{ $item['height'] / max($item['width'], $item['height']) }});">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div>