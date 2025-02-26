@props(['title', 'icon', 'items'])

<div class="space-y-2">
    @if($title)
        <h3 class="font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">
            <x-icon :name="$icon" class="w-5 h-5 text-indigo-500" />
            {{ $title }}
        </h3>
    @endif

    @foreach($items as $item)
        <div class="flex justify-between text-sm items-center">
            <span class="text-slate-500 flex items-center gap-2">
                <x-icon :name="$item['icon']" class="w-4 h-4 {{ $item['iconColor'] }}" />
                {{ $item['label'] }}:
            </span>
            <span class="font-medium {{ $item['valueColor'] }}">{{ $item['value'] }}</span>
        </div>
    @endforeach
</div>
