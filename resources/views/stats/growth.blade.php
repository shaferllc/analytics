@php
    $growth = calcGrowth($growthCurrent, $growthPrevious);
    $growthFormatted = str_replace(['-', __('.') . '0'], '', number_format($growth, 1, __('.'), __(','))); 
@endphp

@if ($growth > 0)
    <div class="d-flex align-items-center text-truncate text-success">
        <div class="d-flex align-items-center justify-content-center width-4 height-4 {{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">
            <x-icon name="arrow-trending-up" class="fill-current w-3 h-3" />
        </div>
        <div class="text-truncate">{{ $growthFormatted }}%</div>
    </div>
@elseif ($growth < 0)
    <div class="d-flex align-items-center text-truncate text-danger">
        <div class="d-flex align-items-center justify-content-center width-4 height-4 {{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">
            <x-icon name="arrow-trending-down" class="fill-current w-3 h-3" />
        </div>
        <div class="text-truncate">{{ $growthFormatted }}%</div>
    </div>
@else
    @if ($growthCurrent == $growthPrevious && $growthCurrent > 0)
        <div class="text-muted">—</div>
    @elseif (!$growthPrevious)
        <div class="text-muted text-truncate">{{ __('No prior data') }}</div>
    @else
        <div class="text-muted text-truncate">{{ __('No current data') }}</div>
    @endif
@endif