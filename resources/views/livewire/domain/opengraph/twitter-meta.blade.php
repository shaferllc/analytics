<div class="bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 dark:from-indigo-900 dark:via-purple-900 dark:to-pink-900 rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition-all duration-300 border border-indigo-200 dark:border-indigo-800">
    <h3 class="text-2xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-300 dark:to-purple-300 flex items-center">
        <x-icon name="heroicon-o-star" class="w-7 h-7 mr-3 text-indigo-500 animate-pulse" />
        Twitter Metadata
    </h3>

    @php
        $twitterMetadataItems = [
            [
                'label' => 'Card',
                'value' => $opengraphAnalysis['twitter']['card'] ?? 'Not found',
                'icon' => 'heroicon-o-star',
                'color' => 'yellow',
            ],
            [
                'label' => 'Site',
                'value' => $opengraphAnalysis['twitter']['site'] ?? 'Not found',
                'icon' => 'heroicon-o-user-group',
                'color' => 'blue',
            ],
            [
                'label' => 'Title',
                'value' => $opengraphAnalysis['twitter']['title'] ?? 'Not found',
                'icon' => 'heroicon-o-user-circle',
                'color' => 'green',
            ],
            [
                'label' => 'Description',
                'value' => $opengraphAnalysis['twitter']['description'] ?? 'Not found',
                'icon' => 'heroicon-o-document',
                'color' => 'fuchsia',
            ]
        ];
    @endphp

    <div class="space-y-4">
        @foreach($twitterMetadataItems as $item)
            <div class="flex justify-between items-center p-3 bg-white/50 dark:bg-gray-800/50 rounded-lg hover:bg-white/80 dark:hover:bg-gray-800/80 transition-all duration-300 border-l-4 border-{{ $item['color'] }}-400">
                <span class="text-gray-700 dark:text-gray-300 flex items-center font-medium">
                    <x-icon name="{{ $item['icon'] }}" class="w-5 h-5 mr-2 text-{{ $item['color'] }}-500" />
                    {{ $item['label'] }}
                </span>
                <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ $item['value'] }}</span>
            </div>
        @endforeach
    </div>
</div>