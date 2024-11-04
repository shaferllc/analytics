<div class="bg-white/80 dark:bg-gray-800/90 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-gray-200/50 dark:border-gray-700/50 mt-8">
    <div class="flex items-center gap-4 mb-6">
        <div class="p-3 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl shadow-lg shadow-cyan-500/20">
            <x-icon name="heroicon-o-presentation-chart-line" class="w-6 h-6 text-white" />
        </div>
        <h3 class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-cyan-600 to-blue-600">{{ __('Analytics') }}</h3>
    </div>
    
    <div class="space-y-4">
        <!-- Average Certificate Lifetime -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <div class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-purple-500 to-indigo-600 mb-1">{{ __('Avg. Certificate Lifetime') }}</div>
            <div class="text-2xl font-bold">
                {{ Number::format($website->certificates->avg(function($cert) {
                    return $cert->valid_from && $cert->valid_to ? $cert->valid_from->diffInDays($cert->valid_to) : 0;
                }) ?? 0) }} {{ __('days') }}
            </div>
        </div>

        <!-- Renewal Frequency -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <div class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-emerald-500 to-teal-600 mb-1">{{ __('Renewal Frequency') }}</div>
            <div class="text-2xl font-bold">
                {{ $website->certificates->where('created_at', '>', now()->subYear())->count() }}
                <span class="text-sm text-gray-500">/ {{ __('year') }}</span>
            </div>
        </div>

        <!-- Certificate Provider Distribution -->
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium bg-clip-text text-transparent bg-gradient-to-r from-cyan-600 to-blue-600">{{ __('Provider Distribution') }}</div>
                <x-icon name="heroicon-o-building-office" class="w-4 h-4 text-gray-400" />
            </div>
            @php
                $providers = $website->certificates
                    ->groupBy('issuer')
                    ->reject(fn($certs) => $certs->first()->issuer == 'Unknown')
                    ->map(function($certs) use ($website) {
                        return [
                            'count' => $certs->count(),
                            'percentage' => ($certs->count() / $website->certificates->count()) * 100
                        ];
                    });
            @endphp
            
            <div class="space-y-2">
                @forelse($providers as $issuer => $data)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $issuer }}</span>
                            <span>{{ number_format($data['percentage'], 1) }}%</span>
                        </div>
                        <div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $data['percentage'] }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('No providers found') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>