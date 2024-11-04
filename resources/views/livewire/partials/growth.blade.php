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

{{-- Explosive growth (>50%) - animated fireworks and rocket --}}
@if ($growth > 50)
    <div class="flex items-center truncate text-green-500 animate-[wiggle_1s_ease-in-out_infinite]" title="{{ $tooltipText }}">
        <div class="flex items-center justify-center w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full transform hover:scale-150 transition-all duration-500 animate-[bounce_0.5s_ease-in-out_infinite]">
            <x-icon name="heroicon-o-arrow-trending-up" class="fill-current w-4 h-4 animate-[spin_2s_linear_infinite]" />
        </div>
        <div class="truncate ml-2 animate-[pulse_1s_ease-in-out_infinite]">
            <span class="font-extrabold text-2xl bg-gradient-to-r from-green-500 via-emerald-400 to-teal-500 bg-clip-text text-transparent shadow-lg animate-[bounce_0.8s_ease-in-out_infinite]">{{ $growthFormatted }}%</span>
            <span class="font-medium animate-[flash_1.2s_ease-in-out_infinite]">EXPLOSIVE GROWTH</span>
        </div>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic flex items-center space-x-1 animate-[slideInUp_0.5s_ease-out]">
        <span class="text-lg animate-[tada_1s_ease-in-out_infinite]">🚀</span>
        <span class="animate-[pulse_2s_ease-in-out_infinite]">Phenomenal growth from {{ $growthPrevious }}! Breaking records!</span>
    </div>
    @if($streakMessage)
        <div class="ml-2 text-sm font-bold text-amber-500 animate-[pulse_1s_ease-in-out_infinite] hover:text-amber-600 transition-colors duration-300">{{ $streakMessage }}</div>
    @endif

{{-- Strong growth (10-50%) - animated up arrow with stars --}}
@elseif ($growth >= 10)
    <div class="flex items-center truncate text-green-500 animate-[slideInRight_0.5s_ease-out]" title="{{ $tooltipText }}">
        <div class="flex items-center justify-center w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full transform hover:scale-125 transition-all duration-300 animate-[bounce_0.7s_ease-in-out_infinite]">
            <x-icon name="heroicon-o-arrow-trending-up" class="fill-current w-4 h-4 animate-[pulse_1s_ease-in-out_infinite]" />
        </div>
        <div class="truncate ml-2 animate-[fadeIn_1s_ease-in-out]">
            <span class="font-extrabold text-2xl bg-gradient-to-r from-green-500 to-emerald-500 bg-clip-text text-transparent shadow-lg animate-[heartBeat_1.3s_ease-in-out_infinite]">{{ $growthFormatted }}%</span>
            <span class="font-medium animate-[flash_1.5s_ease-in-out_infinite]">STRONG GROWTH</span>
        </div>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic flex items-center space-x-1 animate-[fadeInUp_0.8s_ease-out]">
        <span class="text-lg animate-[swing_2s_ease-in-out_infinite]">📈</span>
        <span class="animate-[pulse_1.8s_ease-in-out_infinite]">Impressive climb from {{ $growthPrevious }}!</span>
    </div>
    @if($streakMessage)
        <div class="ml-2 text-sm font-bold text-amber-500 hover:text-amber-600 transition-colors duration-300 animate-[pulse_1s_ease-in-out_infinite] hover:scale-105 transform">{{ $streakMessage }}</div>
    @endif

{{-- Modest growth (0-10%) - gentle up arrow --}}
@elseif ($growth > 0)
    <div class="flex items-center truncate text-green-500 animate-[fadeInLeft_0.7s_ease-out]" title="{{ $tooltipText }}">
        <div class="flex items-center justify-center w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full transform hover:scale-110 transition-all duration-300 animate-[bounce_1s_ease-in-out_infinite]">
            <x-icon name="heroicon-o-arrow-trending-up" class="fill-current w-4 h-4 animate-[pulse_1.5s_ease-in-out_infinite]" />
        </div>
        <div class="truncate ml-2 animate-[fadeIn_1.2s_ease-in-out]">
            <span class="font-extrabold text-2xl bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent shadow-lg animate-[heartBeat_2s_ease-in-out_infinite]">{{ $growthFormatted }}%</span>
            <span class="font-medium animate-[flash_2s_ease-in-out_infinite]">STEADY GROWTH</span>
        </div>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic flex items-center space-x-1 animate-[slideInUp_0.6s_ease-out]">
        <span class="text-lg animate-[bounce_1.5s_ease-in-out_infinite]">💪</span>
        <span class="animate-[pulse_2s_ease-in-out_infinite]">Growing steadily from {{ $growthPrevious }}</span>
      
    </div>
    @if($streakMessage)
            <div class="ml-2 text-sm font-bold text-amber-500 hover:text-amber-600 transition-colors duration-300 animate-[pulse_1s_ease-in-out_infinite] hover:scale-105 transform">{{ $streakMessage }}</div>
        @endif

{{-- Minor decline (0 to -10%) - gentle down arrow --}}
@elseif ($growth >= -10)
    <div class="flex items-center truncate text-orange-500 animate-[shake_0.8s_ease-in-out_infinite]" title="{{ $tooltipText }}">
        <div class="flex items-center justify-center w-6 h-6 bg-orange-100 dark:bg-orange-900 rounded-full transform hover:scale-110 transition-all duration-300 animate-[pulse_1s_ease-in-out_infinite]">
            <x-icon name="heroicon-o-arrow-trending-down" class="fill-current w-4 h-4 animate-[spin_3s_linear_infinite]" />
        </div>
        <div class="truncate ml-2 animate-[fadeIn_1s_ease-in-out]">
            <span class="font-extrabold text-2xl bg-gradient-to-r from-orange-500 to-amber-500 bg-clip-text text-transparent shadow-lg animate-[heartBeat_1.5s_ease-in-out_infinite]">{{ $growthFormatted }}%</span>
            <span class="font-medium animate-[flash_1.8s_ease-in-out_infinite]">SLIGHT DIP</span>
        </div>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic flex items-center space-x-1 animate-[slideInUp_0.7s_ease-out]">
        <span class="text-lg animate-[tada_2s_ease-in-out_infinite]">📉</span>
        <span class="animate-[pulse_1.5s_ease-in-out_infinite]">Minor decrease from {{ $growthPrevious }} - monitoring closely</span>
    </div>

{{-- Significant decline (-10 to -25%) - pulsing down arrow --}}
@elseif ($growth >= -25)
    <div class="flex items-center truncate text-red-500 animate-[shake_0.6s_ease-in-out_infinite]" title="{{ $tooltipText }}">
        <div class="flex items-center justify-center w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full transform hover:scale-125 transition-all duration-300 animate-[pulse_0.8s_ease-in-out_infinite]">
            <x-icon name="heroicon-o-arrow-trending-down" class="fill-current w-4 h-4 animate-[spin_1.5s_linear_infinite]" />
        </div>
        <div class="truncate ml-2 animate-[fadeIn_0.8s_ease-in-out]">
            <span class="font-extrabold text-2xl bg-gradient-to-r from-red-500 to-rose-500 bg-clip-text text-transparent shadow-lg animate-[heartBeat_1s_ease-in-out_infinite]">{{ $growthFormatted }}%</span>
            <span class="font-medium animate-[flash_1.3s_ease-in-out_infinite]">NOTABLE DECLINE</span>
        </div>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic flex items-center space-x-1 animate-[slideInUp_0.5s_ease-out]">
        <span class="text-lg animate-[wobble_1.5s_ease-in-out_infinite]">⚡</span>
        <span class="animate-[pulse_1.2s_ease-in-out_infinite]">Significant drop from {{ $growthPrevious }} - attention needed</span>
    </div>

{{-- Severe decline (< -25%) - urgent warning indicators --}}
@elseif ($growth < -25)
    <div class="flex items-center truncate text-red-600 animate-[shake_0.4s_ease-in-out_infinite]" title="{{ $tooltipText }}">
        <div class="flex items-center justify-center w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full transform hover:scale-150 transition-all duration-300 animate-[ping_0.6s_ease-in-out_infinite]">
            <x-icon name="heroicon-o-arrow-trending-down" class="fill-current w-4 h-4 animate-[spin_1s_linear_infinite]" />
        </div>
        <div class="truncate ml-2 animate-[fadeIn_0.6s_ease-in-out]">
            <span class="font-extrabold text-2xl bg-gradient-to-r from-red-600 via-rose-600 to-pink-600 bg-clip-text text-transparent shadow-lg animate-[heartBeat_0.8s_ease-in-out_infinite]">{{ $growthFormatted }}%</span>
            <span class="font-medium animate-[flash_1s_ease-in-out_infinite]">CRITICAL DECLINE</span>
        </div>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic flex items-center space-x-1 animate-[slideInUp_0.4s_ease-out]">
        <span class="text-lg animate-[bounce_0.8s_ease-in-out_infinite]">🚨</span>
        <span class="animate-[pulse_0.8s_ease-in-out_infinite]">Major decline from {{ $growthPrevious }} - immediate action required!</span>
    </div>

{{-- Equal non-zero values - balanced scales --}}
@elseif ($growthCurrent == $growthPrevious && $growthCurrent > 0)
    <div class="flex items-center text-gray-600 dark:text-gray-400 font-medium animate-[fadeIn_1s_ease-in-out]" title="{{ $tooltipText }}">
        <div class="flex items-center justify-center w-6 h-6 bg-yellow-100 dark:bg-yellow-900 rounded-full mr-2 animate-[bounce_1.5s_ease-in-out_infinite]">
            <span class="text-yellow-500 animate-[spin_3s_linear_infinite]">⚖️</span>
        </div>
        <span class="font-extrabold text-2xl bg-gradient-to-r from-yellow-500 via-amber-400 to-orange-500 bg-clip-text text-transparent shadow-lg animate-[pulse_2s_ease-in-out_infinite]">{{ $growthPrevious }}</span>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic flex items-center space-x-1 animate-[slideInUp_0.8s_ease-out]">
        <span class="text-lg animate-[tada_2s_ease-in-out_infinite]">🎯</span>
        <span class="animate-[pulse_1.8s_ease-in-out_infinite]">Perfect balance - maintaining momentum</span>
    </div>

{{-- No previous data - new metrics --}}
@elseif (!$growthPrevious)
    <div class="flex items-center text-gray-600 dark:text-gray-400 font-medium animate-[fadeInUp_1s_ease-out]" title="{{ $tooltipText }}">
        <div class="flex items-center justify-center w-6 h-6 bg-blue-100 dark:bg-blue-900 rounded-full mr-2 animate-[pulse_1s_ease-in-out_infinite]">
            <span class="text-blue-500 animate-[bounce_1.5s_ease-in-out_infinite]">📊</span>
        </div>
        <span class="font-extrabold text-2xl bg-gradient-to-r from-blue-500 via-cyan-400 to-sky-500 bg-clip-text text-transparent shadow-lg animate-[heartBeat_2s_ease-in-out_infinite]">{{ __('Fresh Start - Building History') }}</span>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic flex items-center space-x-1 animate-[slideInUp_0.6s_ease-out]">
        <span class="text-lg animate-[tada_1.5s_ease-in-out_infinite]">✨</span>
        <span class="animate-[pulse_1.5s_ease-in-out_infinite]">Beginning our journey - watch us grow!</span>
    </div>

{{-- No current data - awaiting updates --}}
@else
    <div class="flex items-center text-gray-600 dark:text-gray-400 font-medium animate-[fadeIn_1.2s_ease-in-out]" title="{{ $tooltipText }}">
        <div class="flex items-center justify-center w-6 h-6 bg-purple-100 dark:bg-purple-900 rounded-full mr-2 animate-[spin_2s_linear_infinite]">
            <span class="text-purple-500 animate-[pulse_1.5s_ease-in-out_infinite]">⏳</span>
        </div>
        <span class="font-extrabold text-2xl bg-gradient-to-r from-purple-500 via-violet-400 to-indigo-500 bg-clip-text text-transparent shadow-lg animate-[heartBeat_1.8s_ease-in-out_infinite]">{{ __('Data Collection in Progress') }}</span>
    </div>
    <div class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic flex items-center space-x-1 animate-[slideInUp_0.7s_ease-out]">
        <span class="text-lg animate-[bounce_1.2s_ease-in-out_infinite]">🔄</span>
        <span class="animate-[pulse_1.6s_ease-in-out_infinite]">Gathering insights - refresh soon</span>
    </div>
@endif