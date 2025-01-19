@php
    $browserData = $this->getBrowserData()['data'];
    $firstBrowserData =  $this->getBrowserData()['first'];
    $lastBrowserData = $this->getBrowserData()['last'];
    $aggregates = $this->getBrowserData()['aggregates'];
@endphp
<div x-show="activeTab === 'browsers'">
    <div class="relative p-6 mb-8 text-indigo-200 bg-gradient-to-br from-indigo-900/40 to-indigo-800/20 rounded-xl border border-indigo-700/50 shadow-lg">
        <div class="absolute -top-3 -left-3">
            <div class="p-3 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-500/30 animate-pulse">
                <x-icon name="heroicon-o-globe-alt" class="w-6 h-6 text-white" />
            </div>
        </div>
        
        <div class="ml-8">
            <h3 class="font-bold text-xl text-white mb-2 tracking-wide">
                {{ __('Browser analytics and capabilities') }}
            </h3>
            <p class="text-sm text-indigo-300 leading-relaxed">
                {{ __('Browser versions and supported features, including plugins, languages, color schemes and WebGL support. This data helps understand your visitors\' browser environment.') }}
            </p>
        </div>
        
        <div class="absolute top-0 right-0 h-full w-1/3 bg-gradient-to-l from-indigo-500/10 to-transparent rounded-r-xl"></div>
    </div>
    <div class="space-y-4">
        @foreach($browserData as $data)
            @php
                $metrics = [
                    'resolution' => ['icon' => 'rectangle-stack', 'label' => 'Resolutions', 'suffix' => ''],
                    'plugins' => ['icon' => 'puzzle-piece', 'label' => 'Plugins', 'suffix' => ''],
                    'languages' => ['icon' => 'language', 'label' => 'Languages', 'suffix' => ''],
                    'user_agent' => ['icon' => 'computer-desktop', 'label' => 'User Agents', 'suffix' => ''],
                    'colorScheme' => ['icon' => 'swatch', 'label' => 'Color Schemes', 'suffix' => ''],
                    //'memory_usage' => ['icon' => 'cpu-chip', 'label' => 'Memory Usage', 'suffix' => 'MB'],
                    'devicePixelRatio' => ['icon' => 'square-2-stack', 'label' => 'Device Pixel Ratio', 'suffix' => ''],
                    'webgl' => ['icon' => 'cube-transparent', 'label' => 'WebGL Support', 'suffix' => '']
                ];
            @endphp

<div class="flex items-center justify-between p-6 border border-indigo-800 rounded-lg bg-indigo-900/50 hover:bg-indigo-900/70 transition">
                <div class="flex-1">
                    <div class="flex items-center gap-6">
                        <div class="flex-shrink-0">
                            <x-tooltip text="{{ ucfirst($data->title) }}">
                                <img src="{{ asset('vendor/analytics/icons/browsers/' . $data->icon . '.svg') }}" 
                                    class="w-7 h-7 hover:scale-110 hover:rotate-3 transform transition-all duration-300 ease-in-out hover:drop-shadow-lg relative before:absolute before:inset-0 before:bg-gradient-to-r before:from-transparent before:via-white/20 before:to-transparent before:animate-[shimmer_2s_infinite] before:translate-x-[-100%] before:skew-x-[-15deg]" 
                                    alt="{{ ucfirst($data->title) }} icon" />
                            </x-tooltip>
                        </div>
                        
                        <div class="min-w-0">
                            <div class="flex items-center gap-3">
                                <h4 class="font-semibold text-lg text-indigo-100">{{ ucfirst($data->title) ?: __('Unknown') }}</h4>
                                <x-tooltip text="Percentage of total visits">
                                    <span 
                                        x-data="{ show: false, hover: false }"
                                        x-init="setTimeout(() => show = true, 100)"
                                        x-show="show"
                                        @mouseenter="hover = true"
                                        @mouseleave="hover = false"
                                        x-transition:enter="transition ease-out duration-500"
                                        x-transition:enter-start="opacity-0 transform -translate-y-4 scale-75"
                                        x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                                        :class="{ 'animate-pulse shadow-lg shadow-indigo-500/50': hover }"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-700/50 text-indigo-200 hover:bg-indigo-600/50 transition-all duration-300 hover:scale-110 hover:-translate-y-1 hover:rotate-2"
                                    >
                                        @if($aggregates['total_count'] > 0)
                                            {{ number_format(($data->count / $aggregates['total_count']) * 100, 1) }}%
                                        @else
                                            {{ __('0%') }}
                                        @endif
                                    </span>
                                </x-tooltip>
                            </div>
                            @include('analytics::livewire.partials.page.metrics')
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-8 ml-6">
                    <x-tooltip text="Percentage of total visits">
                        <div class="w-40 h-3 rounded-full bg-indigo-700/30 overflow-hidden relative">
                            <div style="width:0%" 
                                class="h-full bg-gradient-to-r from-indigo-400 to-indigo-600 transition-all duration-1000 ease-out hover:from-indigo-500 hover:to-indigo-700" 
                                x-data="{ hover: false }"
                                @mouseenter="hover = true" 
                                @mouseleave="hover = false"
                                :class="{ 'animate-pulse shadow-lg shadow-indigo-500/50': hover }"
                                x-init="setTimeout(() => {
                                    $el.style.width = '{{ $aggregates['total_count'] > 0 ? ($data->count / $aggregates['total_count']) * 100 : 0 }}%';
                                    $el.classList.add('scale-x-100');
                                }, 100)"
                                class="transform scale-x-0 origin-left">
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center text-xs text-indigo-200 font-medium">
                                {{ number_format($aggregates['total_count'] > 0 ? ($data->count / $aggregates['total_count']) * 100 : 0, 1) }}%
                            </div>
                        </div>
                    </x-tooltip>
                    <div class="flex flex-col gap-1 w-32 text-right">
                        <x-tooltip text="Total visits">
                            <div class="flex items-center justify-end gap-2">
                                <x-icon name="heroicon-o-users" class="w-4 h-4 text-indigo-400" />
                                <span class="font-semibold text-indigo-100">{{ number_format($data->count, 0, __('.'), __(',')) }}</span>
                            </div>
                        </x-tooltip>
                        <x-tooltip text="Unique sessions">
                            <div class="flex items-center justify-end gap-2">
                                <x-icon name="heroicon-o-user" class="w-4 h-4 text-indigo-400" />
                                <span class="text-sm text-indigo-300">{{ number_format($data->unique_sessions, 0, __('.'), __(',')) }}</span>
                            </div>
                        </x-tooltip>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>