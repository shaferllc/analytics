{{-- This component displays dynamic growth metrics with visual indicators --}}
{{-- Shows animated arrows and bold stats to emphasize growth/decline --}}
@php
    // Calculate percentage change between current and previous values
    $growth = calcGrowth($growthCurrent, $growthPrevious);
    // Format the percentage nicely - remove negative signs and unnecessary decimals
    $growthFormatted = str_replace(['-', __('.') . '0'], '', number_format($growth, 1, __('.'), __(','))); 
    // Add tooltip text based on growth value
    $tooltipText = match(true) {
        $growth > 50 => 'Exceptional performance!',
        $growth >= 10 => 'Strong positive trend',
        $growth > 0 => 'Steady improvement',
        $growth >= -10 => 'Minor setback',
        $growth >= -25 => 'Concerning trend',
        default => 'Critical attention needed'
    };

    // NEW: Calculate streak of consecutive growth periods
    $streak = session()->get('growth_streak.' . $type, 0);
    if ($growth > 0) {
        $streak++;
    } else {
        $streak = 0;
    }
    session()->put('growth_streak.' . $type, $streak);

    // NEW: Get motivational message based on streak
    $streakMessage = match(true) {
        $streak >= 10 => "🏆 Incredible {$streak}-period winning streak!",
        $streak >= 5 => "🔥 On fire! {$streak} periods of growth!",
        $streak >= 3 => "⚡ {$streak} growth periods in a row!",
        $streak > 0 => "✨ {$streak} period growth streak started!",
        default => null
    };
@endphp
<div class="flex-1 bg-gray-700 rounded-2xl shadow-lg border border-gray-600 hover:border-blue-500 transform hover:-translate-y-1 transition-all duration-300 p-4">
    <div class="flex items-center justify-between gap-4 mb-4">
        <div class="flex items-center gap-4">
            <div class="p-2 bg-gray-800 rounded-xl shadow-md ring-1 ring-blue-500">
                @if ($growth > 50)
                    <x-icon name="heroicon-o-arrow-trending-up" class="w-10 h-10 text-green-500" />
                @elseif ($growth >= 10)
                    <x-icon name="heroicon-o-arrow-trending-up" class="w-10 h-10 text-green-400" />
                @elseif ($growth > 0)
                    <x-icon name="heroicon-o-arrow-trending-up" class="w-10 h-10 text-green-300" />
                @elseif ($growth >= -10)
                    <x-icon name="heroicon-o-arrow-trending-down" class="w-10 h-10 text-orange-400" />
                @elseif ($growth >= -25)
                    <x-icon name="heroicon-o-arrow-trending-down" class="w-10 h-10 text-red-400" />
                @else
                    <x-icon name="heroicon-o-arrow-trending-down" class="w-10 h-10 text-red-500" />
                @endif
            </div>
            <div>
                <h1 class="text-3xl text-white font-bold tracking-tight">
                    {{ $growthFormatted }}% Growth
                </h1>
                <p class="text-base text-gray-200">{{ $tooltipText }}</p>
                <div class="h-0.5 w-16 bg-blue-500 rounded-full mt-1 group-hover:w-24 transition-all"></div>
            </div>
        </div>
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="px-4 py-2 bg-gray-800 text-gray-300 rounded-lg hover:bg-gray-700 hover:text-white transition-colors duration-200 flex items-center gap-2">
                <span>Details</span>
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"></path>
                </svg>
            </button>
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-lg shadow-lg py-1 z-10">
                <div class="block px-4 py-2 text-gray-300">Previous: {{ $growthPrevious }}</div>
                <div class="block px-4 py-2 text-gray-300">Current: {{ $growthCurrent }}</div>
                @if($streakMessage)
                    <div class="block px-4 py-2 text-amber-500">{{ $streakMessage }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-4 sm:grid-cols-4 gap-3">
        <div class="bg-gray-800 p-3 rounded-lg hover:bg-gray-600 transition-colors group">
            <p class="text-sm text-gray-300">Previous Value</p>
            <div class="flex items-center gap-1.5">
                <x-icon name="heroicon-o-clock" class="w-5 h-5 text-blue-500" />
                <p class="text-base font-semibold text-white">{{ $growthPrevious }}</p>
            </div>
        </div>

        <div class="bg-gray-800 p-3 rounded-lg hover:bg-gray-600 transition-colors group">
            <p class="text-sm text-gray-300">Current Value</p>
            <div class="flex items-center gap-1.5">
                <x-icon name="heroicon-o-arrow-path" class="w-5 h-5 text-green-500" />
                <p class="text-base font-semibold text-white">{{ $growthCurrent }}</p>
            </div>
        </div>

        <div class="bg-gray-800 p-3 rounded-lg hover:bg-gray-600 transition-colors group">
            <p class="text-sm text-gray-300">Growth Streak</p>
            <div class="flex items-center gap-1.5">
                <x-icon name="heroicon-o-fire" class="w-5 h-5 text-amber-500" />
                <p class="text-base font-semibold text-white">{{ $streak }} periods</p>
            </div>
        </div>

        <div class="bg-gray-800 p-3 rounded-lg hover:bg-gray-600 transition-colors group">
            <p class="text-sm text-gray-300">Status</p>
            <div class="flex items-center gap-1.5">
                @if ($growth > 0)
                    <x-icon name="heroicon-o-check-circle" class="w-5 h-5 text-green-500" />
                    <p class="text-base font-semibold text-white">Growing</p>
                @else
                    <x-icon name="heroicon-o-exclamation-circle" class="w-5 h-5 text-red-500" />
                    <p class="text-base font-semibold text-white">Declining</p>
                @endif
            </div>
        </div>
    </div>
</div>