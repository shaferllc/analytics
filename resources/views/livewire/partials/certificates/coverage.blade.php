<div class="bg-white/80 dark:bg-gray-800/90 backdrop-blur-lg rounded-2xl p-6 shadow-xl border border-gray-200/50 dark:border-gray-700/50 mb-8">
    <div class="flex items-center gap-4 mb-6">
        <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg shadow-emerald-500/20">
            <x-icon name="heroicon-o-chart-bar" class="w-6 h-6 text-white" />
        </div>
        <h2 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-400 dark:to-teal-400">
            {{ __('Coverage Score') }}
        </h2>
    </div>

    @php
        $totalSubdomains = $website->certificates->count();
        $subdomainsWithValidCerts = $website->certificates->filter(fn($cert) => $cert->was_valid)->count();
        $coverageScore = $totalSubdomains > 0 ? ($subdomainsWithValidCerts / $totalSubdomains) * 100 : 0;
    @endphp

    <div class="text-center">
        <div class="relative inline-flex items-center justify-center w-32 h-32">
            <div @class([
                'absolute inset-0 rounded-full border-8',
                'border-green-200 dark:border-green-900' => $coverageScore >= 80,
                'border-yellow-200 dark:border-yellow-900' => $coverageScore >= 60 && $coverageScore < 80,
                'border-red-200 dark:border-red-900' => $coverageScore < 60
            ])></div>
            <span @class([
                'text-2xl font-bold',
                'text-green-500 dark:text-green-400' => $coverageScore >= 80,
                'text-yellow-500 dark:text-yellow-400' => $coverageScore >= 60 && $coverageScore < 80,
                'text-red-500 dark:text-red-400' => $coverageScore < 60
            ])>
                {{ number_format($coverageScore, 1) }}%
            </span>
        </div>
    </div>
</div>
