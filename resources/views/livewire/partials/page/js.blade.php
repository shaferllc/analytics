<div x-show="activeTab === 'jsError'">
    <div class="space-y-4">
        @foreach($jsErrors['data'] as $error)
            <div class="p-6 border border-indigo-800 rounded-lg bg-indigo-900/50">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-full bg-red-500/20">
                            <x-icon name="heroicon-o-exclamation-triangle" class="w-5 h-5 text-red-400" />
                        </div>
                        <div>
                            <h4 class="font-medium text-indigo-100">{{ $error->type ?: __('Unknown Error') }}</h4>
                            <p class="text-sm text-indigo-400">{{ $error->timestamp ? \Carbon\Carbon::parse($error->timestamp)->diffForHumans() : __('Time unknown') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 text-sm rounded-full bg-indigo-700/50 text-indigo-200">
                            {{ number_format($error->count) }} {{ __('occurrences') }}
                        </span>
                        <span class="px-3 py-1 text-sm rounded-full bg-indigo-700/50 text-indigo-200">
                            {{ number_format($error->unique_sessions) }} {{ __('users') }}
                        </span>
                    </div>
                </div>

                <div class="space-y-4">
                    @if($error->message)
                        <div class="p-4 bg-red-900/20 border border-red-800/50 rounded">
                            <p class="font-mono text-sm text-red-300">{{ $error->message }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4 text-sm">
                        @if($error->source)
                            <div>
                                <span class="block text-indigo-400 mb-1">{{ __('Source') }}</span>
                                <span class="text-indigo-200 font-mono">{{ $error->source }}</span>
                            </div>
                        @endif

                        @if($error->url)
                            <div>
                                <span class="block text-indigo-400 mb-1">{{ __('URL') }}</span>
                                <a href="{{ $error->url }}" target="_blank" class="text-indigo-200 hover:text-indigo-100 font-mono truncate block">
                                    {{ $error->url }}
                                </a>
                            </div>
                        @endif

                        @if($error->error)
                            <div class="col-span-2">
                                <span class="block text-indigo-400 mb-1">{{ __('Error Details') }}</span>
                                <div class="space-y-2">
                                    @if($error->error['name'])
                                        <p class="text-indigo-200 font-mono">{{ __('Name') }}: {{ $error->error['name'] }}</p>
                                    @endif
                                    @if($error->error['message'])
                                        <p class="text-indigo-200 font-mono">{{ __('Message') }}: {{ $error->error['message'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($error->stack && count($error->stack))
                            <div class="col-span-2" x-data="{ showAll: false }">
                                <div class="flex items-center justify-between">
                                    <span class="block text-indigo-400 mb-1">{{ __('Occurrences') }}</span>
                                    @if(count($error->stack) > 3)
                                    <button 
                                        @click="showAll = !showAll"
                                        class="text-sm text-indigo-400 hover:text-indigo-300"
                                    >
                                        <span x-text="showAll ? '{{ __('Show Less') }}' : '{{ __('Show More') }}'"></span>
                                    </button>
                                    @endif
                                </div>
                                <template x-for="(trace, index) in {{ json_encode($error->stack) }}" :key="index">
                                    <div 
                                        x-show="index < 3 || showAll"
                                        class="mt-2 p-3 bg-indigo-800/30 rounded"
                                    >
                                        <div class="font-mono text-sm space-y-2">
                                            <p class="text-indigo-300" x-text="new Date(trace.timestamp).toLocaleString()"></p>
                                            <p class="text-indigo-400" x-text="trace.userAgent"></p>
                                            <template x-if="trace.error">
                                                <div class="text-indigo-300 mt-2">
                                                    <p x-text="trace.error.name || ''"></p>
                                                    <p x-text="trace.error.message || ''"></p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>