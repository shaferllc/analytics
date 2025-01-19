<div x-show="activeTab === 'sessions'">
    <h2 class="text-2xl font-semibold text-indigo-100 mb-4">{{ __('User Sessions') }}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($sessions['data'] as $sessionId => $sessionData)
            <div class="p-6 border border-indigo-800 rounded-lg bg-indigo-900/50 hover:bg-indigo-900/70 transition">
                <div class="mb-4">
                    <h3 class="text-xl font-semibold text-indigo-100">{{ $sessionId }}</h3>
                    <div class="mt-2 text-sm text-indigo-300">
                        <x-icon name="heroicon-o-user" class="w-4 h-4 inline-block mr-1.5" />
                        {{ __('Total Events') }}: {{ number_format(count($sessionData), 0, __('.'), __(',')) }}
                    </div>
                </div>

                <div class="space-y-4">
                    <div x-data="{ showAll: false }">
                        @foreach($sessionData as $index => $data)
                            <div x-data="{ open: false }" class="relative mb-2">
                                <button @click="open = !open" class="flex items-center w-full px-4 py-2 text-left bg-indigo-800/30 rounded-lg hover:bg-indigo-800/50 transition">
                                    <div class="flex-1 text-indigo-200">
                                        <div class="font-medium">{{ Str::of($data['name'])->title()->replace('_', ' ') }}</div>
                                    </div>
                                    <x-icon x-show="!open" name="heroicon-o-chevron-down" class="w-5 h-5 text-indigo-400" />
                                    <x-icon x-show="open" name="heroicon-o-chevron-up" class="w-5 h-5 text-indigo-400" />
                                </button>
                                
                                <div x-show="open" 
                                        x-transition
                                        class="mt-2 px-4 py-2 bg-indigo-800 rounded-lg text-sm text-indigo-200">
                                    <div class="mb-4">
                                        <h4 class="font-semibold mb-2">{{ __('Event Details') }}</h4>
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($data['value'] as $key => $value)
                                                <li>
                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                    @if(is_array($value) || is_object($value))
                                                        <ul class="ml-4 list-disc list-inside">
                                                            @foreach($value as $subKey => $subValue)
                                                                <li>
                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $subKey)) }}:</strong>
                                                                    @if(is_array($subValue) || is_object($subValue))
                                                                        <ul class="ml-4 list-disc list-inside">
                                                                            @foreach($subValue as $subSubKey => $subSubValue)
                                                                                <li>
                                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $subSubKey)) }}:</strong>
                                                                                    @if(is_string($subSubValue))
                                                                                        {{ $subSubValue }}
                                                                                    @elseif(is_array($subSubValue) || is_object($subSubValue))
                                                                                        <ul class="ml-4 list-disc list-inside">
                                                                                            @foreach($subSubValue as $key => $value)
                                                                                                <li>
                                                                                                    <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                                    @if(is_string($value))
                                                                                                        {{ $value }}
                                                                                                    @elseif(is_object($value))
                                                                                                        {{ json_encode($value) }}
                                                                                                    @else
                                                                                                        {{ $value }}
                                                                                                    @endif
                                                                                                </li>
                                                                                            @endforeach
                                                                                        </ul>
                                                                                    @else
                                                                                        {{ $subSubValue }}
                                                                                    @endif
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    @else
                                                                        {{ $subValue }}
                                                                    @endif
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="flex justify-between">
                                        <div><strong>{{ __('Count') }}:</strong> {{ $data['count'] }}</div>
                                        <div><strong>{{ __('Date') }}:</strong> {{ $data['date']->format('Y-m-d H:i:s') }}</div>
                                    </div>
                                </div>
                            </div>
                            @if($index >= 2 && !$loop->last)
                                <div x-show="!showAll" class="text-center mt-2">
                                    <button @click="showAll = true" class="text-indigo-400 hover:text-indigo-300">
                                        {{ __('Show :count more events', ['count' => $sessionData->count() - 3]) }}
                                        <x-icon name="heroicon-o-chevron-down" class="w-5 h-5 inline-block" />
                                    </button>
                                </div>
                                @break
                            @endif
                        @endforeach
                        <div x-show="showAll">
                            @foreach($sessionData->skip(3) as $data)
                                <div x-data="{ open: false }" class="relative mb-2">
                                    <button @click="open = !open" class="flex items-center w-full px-4 py-2 text-left bg-indigo-800/30 rounded-lg hover:bg-indigo-800/50 transition">
                                        <div class="flex-1 text-indigo-200">
                                            <div class="font-medium">{{ Str::of($data['name'])->title()->replace('_', ' ') }}</div>
                                        </div>
                                        <x-icon x-show="!open" name="heroicon-o-chevron-down" class="w-5 h-5 text-indigo-400" />
                                        <x-icon x-show="open" name="heroicon-o-chevron-up" class="w-5 h-5 text-indigo-400" />
                                    </button>
                                    
                                    <div x-show="open" 
                                            x-transition
                                            class="mt-2 px-4 py-2 bg-indigo-800 rounded-lg text-sm text-indigo-200">
                                        <div class="mb-4">
                                            <h4 class="font-semibold mb-2">{{ __('Event Details') }}</h4>
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach($data['value'] as $key => $value)
                                                    <li>
                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                        @if(is_array($value) || is_object($value))
                                                            <ul class="ml-4 list-disc list-inside">
                                                                @foreach($value as $subKey => $subValue)
                                                                    <li>
                                                                        <strong>{{ ucfirst(str_replace('_', ' ', $subKey)) }}:</strong>
                                                                        @if(is_array($subValue) || is_object($subValue))
                                                                            <ul class="ml-4 list-disc list-inside">
                                                                                @foreach($subValue as $subSubKey => $subSubValue)
                                                                                    <li>
                                                                                        <strong>{{ ucfirst(str_replace('_', ' ', $subSubKey)) }}:</strong>
                                                                                        @if(is_string($subSubValue))
                                                                                            {{ $subSubValue }}
                                                                                        @elseif(is_array($subSubValue) || is_object($subSubValue))
                                                                                            <ul class="ml-4 list-disc list-inside">
                                                                                                @foreach($subSubValue as $key => $value)
                                                                                                    <li>
                                                                                                        <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                                                                        @if(is_string($value))
                                                                                                            {{ $value }}
                                                                                                        @elseif(is_object($value))
                                                                                                            {{ json_encode($value) }}
                                                                                                        @else
                                                                                                            {{ $value }}
                                                                                                        @endif
                                                                                                    </li>
                                                                                                @endforeach
                                                                                            </ul>
                                                                                        @else
                                                                                            {{ $subSubValue }}
                                                                                        @endif
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        @else
                                                                            {{ $subValue }}
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="flex justify-between">
                                            <div><strong>{{ __('Count') }}:</strong> {{ $data['count'] }}</div>
                                            <div><strong>{{ __('Date') }}:</strong> {{ $data['date']->format('Y-m-d H:i:s') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="text-center mt-2">
                                <button @click="showAll = false" class="text-indigo-400 hover:text-indigo-300">
                                    {{ __('Show less') }}
                                    <x-icon name="heroicon-o-chevron-up" class="w-5 h-5 inline-block" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>