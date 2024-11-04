@php
    $filterIcons = [
        'user' => 'account-circle'
    ];
@endphp

@foreach($filters as $key => $value)
    <div class="flex items-center mb-3 md:mb-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
        <div class="flex items-center bg-gray-200 rounded-l px-2 py-1">
            @include('icons.' . $filterIcons[$key], ['class' => 'fill-current w-4 h-4'])
        </div>
        <input type="text" class="border border-gray-300 rounded-r px-2 py-1 text-sm" value="{{ $value }}" readonly>
    </div>
    <input type="hidden" name="{{ $key }}_id" value="{{ request()->input($key.'_id') }}">
@endforeach