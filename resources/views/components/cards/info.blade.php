@props([
    'section' => null,
    'data' => null,
])

@php
    $textColors = match($section['color']) {
        'blue' => 'text-blue-500',
        'red' => 'text-red-500',
        'green' => 'text-green-500',
        'yellow' => 'text-yellow-500',
        'purple' => 'text-purple-500',
        default => 'text-blue-500',
    };

    $bgColors = match($section['color']) {
        'blue' => 'bg-blue-50/50 dark:bg-blue-900/20',
        'red' => 'bg-red-50/50 dark:bg-red-900/20',
        'green' => 'bg-green-50/50 dark:bg-green-900/20',
        'yellow' => 'bg-yellow-50/50 dark:bg-yellow-900/20',
        'purple' => 'bg-purple-50/50 dark:bg-purple-900/20',
        default => 'bg-blue-50/50 dark:bg-blue-900/20',
    };

@endphp

<div class="{{ $section['type'] !== 'raw' ? 'mb-8' : '' }}">
    <!-- Section Header -->
    <div class="flex items-center gap-3 mb-6 p-3 bg-white/50 dark:bg-slate-800/50 rounded-lg border border-slate-200/60 dark:border-slate-700/60 shadow-sm hover:shadow-md transition-all">
        <div class="p-2.5 rounded-lg {{ $bgColors }} shadow-inner">
            <x-icon :name="$section['icon']" class="w-6 h-6 {{ $textColors }}" />
        </div>
        <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200 tracking-wide">
            <span class="bg-gradient-to-r from-slate-800 to-slate-700 dark:from-slate-200 dark:to-slate-300">
                {{ $section['title'] }}
            </span>
        </h4>
    </div>

    @if($section['type'] === 'grid')
        <div class="grid grid-cols-2 gap-4">
            @foreach($section['fields'] as $field)
                <div class="bg-white/50 dark:bg-slate-800/50 rounded-lg p-3 hover:shadow-sm transition-all border border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-2 text-sm">
                        @if(isset($field['icon']))
                            <x-icon :name="$field['icon']" class="w-4 h-4 {{ $textColors }}" />
                        @endif
                        <span class="text-slate-500 dark:text-slate-400">{{ $field['label'] }}</span>
                        <div class="ml-auto">
                            <x-popover placement="top-right">
                                <x-slot:trigger>
                                    <x-icon name="fas-info-circle" class="w-4 h-4 text-slate-400 hover:{{ $textColors }} cursor-pointer transition-colors" />
                                </x-slot:trigger>
                                <div class="text-sm max-w-xs p-2">
                                    {!! $field['tooltip'] !!}
                                </div>
                            </x-popover>
                        </div>
                    </div>
                    @if(!empty(data_get($data, $field['key'])))
                        <div class="flex items-center gap-2 font-medium text-slate-700 dark:text-slate-300 text-sm mt-1.5 px-1 py-0.5">
                            @if(isset($field['iconString']))
                                {!! $field['iconString'](data_get($data, $field['key'])) !!}
                            @endif
                            {{ isset($field['transform']) ? $field['transform'](data_get($data, $field['key'])) : data_get($data, $field['key']) }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif($section['type'] === 'key-value')
        <div class="grid grid-cols-2 gap-4">
            @foreach($section['fields'] as $fieldKey => $field)
                <div class="bg-white/50 dark:bg-slate-800/50 rounded-lg p-3 hover:shadow-sm transition-all border border-slate-200/60 dark:border-slate-700/60">
                    <div class="flex items-center gap-2 text-sm">
                        @if(isset($field['icon']))
                            <x-icon :name="$field['icon']" class="w-4 h-4 {{ $textColors }}" />
                        @endif
                        <span class="text-slate-500 dark:text-slate-400">
                            {{ is_array($field) ? $field['label'] : $field }}
                        </span>
                        @if(is_array($field) && isset($field['tooltip']))
                            <div class="ml-auto">
                                <x-popover placement="top-right">
                                    <x-slot:trigger>
                                        <x-icon name="fas-info-circle" class="w-4 h-4 text-slate-400 hover:{{ $textColors }} cursor-pointer transition-colors" />
                                    </x-slot:trigger>
                                    <div class="text-sm max-w-xs p-2">
                                        {!! $field['tooltip'] !!}
                                    </div>
                                </x-popover>
                            </div>
                        @endif
                    </div>
                    @if(Arr::has($data, $fieldKey))
                        <div class="mt-1.5">
                            @if(is_array($field))
                                @foreach($field['transform'](Arr::get($data, $fieldKey)) as $item)
                                    <div class="text-sm flex items-center gap-2 font-medium text-slate-700 dark:text-slate-300 px-1 py-0.5">
                                        @if(isset($field['iconString']))
                                            {!! $field['iconString']($item) !!}
                                        @endif
                                        {{ $item }}
                                    </div>
                                @endforeach
                            @else
                                <div class="text-sm flex items-center gap-2 font-medium text-slate-700 dark:text-slate-300 px-1 py-0.5">
                                    @if(isset($field['iconString']))
                                        {!! $field['iconString'](Arr::get($data, $fieldKey)) !!}
                                    @endif
                                    {{ Arr::get($data, $fieldKey)->join(', ') ?? 'Unknown' }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @elseif($section['type'] === 'raw')
        <div
            x-data="{ copied: false }"
            class="bg-slate-50 dark:bg-slate-900/50 rounded-lg p-3 relative group cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800/70 transition-colors border border-slate-200/60 dark:border-slate-700/60"
            @click="
                navigator.clipboard.writeText(`{{ $section['value'] }}`);
                copied = true;
                setTimeout(() => copied = false, 2000);
            "
        >
            <code class="text-xs text-slate-600 dark:text-slate-400 break-all">{{ $section['value'] }}</code>
            <div
                x-show="copied"
                class="absolute top-2 right-2 bg-green-500/90 text-white text-xs px-2 py-1 rounded"
                x-cloak
            >
                {{ __('Copied!') }}
            </div>
            <div
                x-show="!copied"
                class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-white/50 dark:bg-slate-700/50 backdrop-blur-sm p-1.5 rounded-full border border-slate-200 dark:border-slate-600 hover:border-primary-500 dark:hover:border-primary-500"
            >
                <x-icon name="far-copy" class="w-4 h-4 text-primary-500 hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 transition-colors" />
            </div>
        </div>
    @endif
</div>
