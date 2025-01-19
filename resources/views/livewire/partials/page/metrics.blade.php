<div class="mt-2 flex flex-wrap gap-x-6 gap-y-4 text-sm text-indigo-400">
    @foreach($metrics as $key => $metric)
        @if(!empty($data->$key))
            <div x-data="{ open: false }" class="relative">
                <x-tooltip text="Click to view details">
                    <button @click="open = !open" class="flex items-center text-indigo-400 hover:text-indigo-300">
                        @if($key === 'network_latency')
                            <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
                            </svg>
                        @else
                            <x-icon name="heroicon-o-{{ $metric['icon'] }}" class="w-4 h-4 mr-1.5" />
                        @endif
                        {{ __(Str::title($metric['label'])) }} ({{ count($data->$key) }})
                        <template x-if="open">
                            <x-icon name="heroicon-o-chevron-down" class="w-4 h-4 ml-1" />
                        </template>
                        <template x-if="!open">
                            <x-icon name="heroicon-o-chevron-up" class="w-4 h-4 ml-1" />
                        </template>
                    </button>
                </x-tooltip>
                
                <div x-show="open" 
                        @click.away="open = false"
                        x-transition
                        class="absolute z-10 mt-2 bg-indigo-800 rounded-lg shadow-lg py-2 w-96">
                    <div class="max-h-96 overflow-y-auto">
                    @foreach($data->$key as $item)
                        <div class="px-4 py-2 text-sm text-indigo-200 hover:bg-indigo-700">
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    @if(isset($item['value']))
                                        @if($key === 'webgl')
                                            <span class="font-medium">{{ Str::title($item['value'] ? 'Supported' : 'Not Supported') }}</span>
                                        @elseif(is_array($item['value']))
                                            <div class="space-y-3">
                                                @foreach($item['value'] as $metric => $data)
                                                    <div class="flex flex-col gap-1">
                                                        <div class="flex justify-between p-2 {{ $loop->even ? 'bg-indigo-800/50' : 'bg-indigo-900/50' }} rounded">
                                                            <span class="text-indigo-300">{{ Str::title($metric) }}</span>
                                                            <span>{{ is_array($data) ? $data['value'] : $data }}{{ $metric['suffix'] ?? '' }}</span>
                                                        </div>
                                                        @if(is_array($data) && isset($data['description']))
                                                            <div class="text-xs text-indigo-300/70 px-2">{{ $data['description'] }}</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="font-medium">{{ Str::title($item['value']) }}{{ $metric['suffix'] ?? '' }}</span>
                                        @endif
                                    @else
                                        <div class="space-y-1">
                                            @foreach((array)$item as $name => $value)
                                                <div class="flex justify-between">
                                                    <span class="text-indigo-300">{{ Str::headline($name) }}</span>
                                                    <span>{{ isset($value) ? Str::title($value) : 'N/A' }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <x-tooltip text="Number of occurrences" position="left">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-600 text-indigo-200">
                                            {{ number_format($item['count'] ?? 1) }}
                                        </span>
                                    </x-tooltip>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</div>