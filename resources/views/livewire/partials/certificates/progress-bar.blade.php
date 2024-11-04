@if($certificate->valid_from && $certificate->valid_to)
    @php
        $totalDays = $certificate->valid_from && $certificate->valid_to ? $certificate->valid_from->diffInDays($certificate->valid_to) : 0;
        $daysLeft = $certificate->valid_to ? now()->diffInDays($certificate->valid_to) : 0;
        $percentage = $totalDays > 0 ? min(100, max(0, ($daysLeft / $totalDays) * 100)) : 0;
    @endphp
    
    <div class="mb-4">
        <div class="flex justify-between mb-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Validity Period') }}</span>
            @if(!$certificate->did_expire)
                <span class="text-sm font-bold {{ $percentage > 50 ? 'text-green-600' : 'text-red-600' }}">
                    <x-icon name="heroicon-o-clock" class="w-4 h-4 inline mr-1" />
                    {{ Number::format($daysLeft, 0) }} {{ __('days remaining') }}
                </span>
            @endif
        </div>
        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full">
            <div class="h-full rounded-full transition-all {{ 
                $percentage > 75 ? 'bg-green-500' : 
                ($percentage > 50 ? 'bg-yellow-500' : 
                ($percentage > 25 ? 'bg-orange-500' : 'bg-red-500')) 
            }}" style="width: {{ $percentage }}%"></div>
        </div>
    </div>
@endif