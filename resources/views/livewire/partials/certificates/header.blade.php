<div class="flex items-center justify-between mb-4 group hover:scale-[1.01] transition-all duration-300">
    <div class="flex items-center gap-4">
        <div class="{{ $certificate ? ($certificate->was_valid ? ($certificate->valid_to > now()->addDays(30) ? 'bg-gradient-to-br from-green-400 to-green-600' : 'bg-gradient-to-br from-purple-400 to-purple-600') : 'bg-gradient-to-br from-red-400 to-red-600') : 'bg-gradient-to-br from-gray-400 to-gray-600' }} p-3 rounded-xl shadow-lg transform group-hover:scale-110 transition-all duration-300">
            <x-icon name="{{ $certificate ? ($certificate->was_valid ? ($certificate->valid_to > now()->addDays(30) ? 'heroicon-o-shield-check' : 'heroicon-o-clock') : 'heroicon-o-shield-exclamation') : 'heroicon-o-x-circle' }}" class="w-6 h-6 text-white animate-pulse" />
        </div>
        <div class="transform group-hover:translate-x-2 transition-transform duration-300">
            <h3 class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600 dark:from-gray-100 dark:to-gray-400">{{ $certificate ? $certificate->domain : $website->domain }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 group-hover:text-blue-500 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $certificate ? $certificate->issuer && $certificate->issuer != 'Unknown' ? $certificate->issuer : __('No Certificate') : __('No Certificate') }}</p>
        </div>
    </div>
    <span class="px-4 py-2 rounded-full text-sm font-bold shadow-md hover:shadow-lg transition-shadow duration-300 {{ $certificate ? ($certificate->was_valid ? ($certificate->valid_to > now()->addDays(30) ? 'bg-gradient-to-r from-green-100 to-green-200 text-green-800 dark:from-green-900 dark:to-green-800 dark:text-green-200' : 'bg-gradient-to-r from-purple-100 to-purple-200 text-purple-800 dark:from-purple-900 dark:to-purple-800 dark:text-purple-200') : 'bg-gradient-to-r from-red-100 to-red-200 text-red-800 dark:from-red-900 dark:to-red-800 dark:text-red-200') : 'bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 dark:from-gray-900 dark:to-gray-800 dark:text-gray-200' }} transform hover:scale-105 transition-transform duration-300">
        {{ $certificate ? ($certificate->was_valid ? ($certificate->valid_to > now()->addDays(30) ? __('Valid') : __('Expiring Soon')) : __('Expired')) : __('No Certificate') }}
    </span>
</div>