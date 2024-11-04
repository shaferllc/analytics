   <!-- Basic Metadata -->
   <div class="bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 dark:from-indigo-900 dark:via-purple-900 dark:to-pink-900 rounded-xl shadow-lg p-6 transform hover:scale-[1.02] transition-all duration-300 border border-indigo-200 dark:border-indigo-800">
        <h3 class="text-2xl font-bold mb-6 text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-300 dark:to-purple-300 flex items-center">
            <x-icon name="heroicon-o-document-text" class="w-7 h-7 mr-3 text-indigo-500 animate-pulse" />
            Basic Metadata
        </h3>
        <div class="space-y-4">
            @php
            $metadataItems = [
                [
                    'label' => 'Canonical',
                    'value' => $opengraphAnalysis['canonical'] ?? 'Not found',
                    'icon' => 'heroicon-o-link',
                    'color' => 'cyan',
                ],
                [
                    'label' => 'Card',
                    'value' => $opengraphAnalysis['card'] ?? 'Not found',
                    'icon' => 'heroicon-o-tag',
                    'color' => 'pink',
                ],
                [
                    'label' => 'Context',
                    'value' => $opengraphAnalysis['@context'] ?? 'Not found',
                    'icon' => 'heroicon-o-globe-alt',
                    'color' => 'blue',
                ],
                [
                    'label' => 'Description',
                    'value' => $opengraphAnalysis['additional']['description'] ?? 'Not found',
                    'icon' => 'heroicon-o-document',
                    'color' => 'fuchsia',
                ],
                [
                    'label' => 'Favicon',
                    'value' => '<img src="data:image/png;base64,' . base64_encode(file_get_contents($opengraphAnalysis['favicon'])) . '" alt="Favicon" />',
                    'icon' => 'heroicon-o-star',
                    'color' => 'yellow',
                ],
                [
                    'label' => 'Locale',
                    'value' => $opengraphAnalysis['additional']['locale'] ?? 'Not found',
                    'icon' => 'heroicon-o-language',
                    'color' => 'purple',
                ],
                [
                    'label' => 'Robots',
                    'value' => $opengraphAnalysis['robots'] ?? 'Not found',
                    'icon' => 'heroicon-o-cog',
                    'color' => 'green',
                ],
                [
                    'label' => 'Site',
                    'value' => $opengraphAnalysis['site'] ?? 'Not found',
                    'icon' => 'heroicon-o-globe-alt',
                    'color' => 'blue',
                ],
                [
                    'label' => 'Site Name', 
                    'value' => $opengraphAnalysis['additional']['site_name'] ?? 'Not found',
                    'icon' => 'heroicon-o-building-office',
                    'color' => 'indigo',
                ],
                [
                    'label' => 'Title',
                    'value' => $opengraphAnalysis['additional']['title'] ?? 'Not found',
                    'icon' => 'heroicon-o-document-text',
                    'color' => 'violet',
                ],
                [
                    'label' => 'Type',
                    'value' => $opengraphAnalysis['additional']['type'] ?? 'Not found', 
                    'icon' => 'heroicon-o-tag',
                    'color' => 'pink',
                ],
                [
                    'label' => 'Url',
                    'value' => $opengraphAnalysis['additional']['url'] ?? 'Not found',
                    'icon' => 'heroicon-o-link',
                    'color' => 'cyan',
                ],
                [
                    'label' => 'Url',
                    'value' => $opengraphAnalysis['url'] ?? 'Not found',
                    'icon' => 'heroicon-o-link',
                    'color' => 'cyan',
                ],
                [
                    'label' => 'Viewport',
                    'value' => $opengraphAnalysis['viewport'] ?? 'Not found',
                    'icon' => 'heroicon-o-eye',
                    'color' => 'green',
                ],
                [
                    'label' => 'Keywords',
                    'value' => $opengraphAnalysis['keywords'] ?? 'Not found',
                    'icon' => 'heroicon-o-tag',
                    'color' => 'pink',
                ],
                
                [
                    'label' => 'Html',
                    'value' => $opengraphAnalysis['html'] ?? 'Not found',
                    'icon' => 'heroicon-o-code-bracket',
                    'color' => 'purple',
                ],
                [
                    'label' => 'Image',
                    'value' => $opengraphAnalysis['image'] ?? 'Not found',
                    'icon' => 'heroicon-o-photo',
                    'color' => 'yellow',
                ],
                [
                    'label' => 'Width',
                    'value' => $opengraphAnalysis['width'] ?? 'Not found',
                    'icon' => 'heroicon-o-square-2-stack',
                    'color' => 'green',
                ],
                [
                    'label' => 'Height',
                    'value' => $opengraphAnalysis['height'] ?? 'Not found',
                    'icon' => 'heroicon-o-square-2-stack',
                    'color' => 'green',
                ],
                [
                    'label' => 'Ratio',
                    'value' => $opengraphAnalysis['ratio'] ?? 'Not found',
                    'icon' => 'heroicon-o-square-2-stack',
                    'color' => 'green',
                ], [
                    'label' => 'Author',
                    'value' => $opengraphAnalysis['author'] ?? 'Not found',
                    'icon' => 'heroicon-o-user-circle',
                    'color' => 'green',
                ],
                [
                    'label' => 'Author URL',
                    'value' => $opengraphAnalysis['author_url'] ?? 'Not found',
                    'icon' => 'heroicon-o-link',
                    'color' => 'cyan',
                ], 
                [
                    'label' => 'CMS',
                    'value' => $opengraphAnalysis['cms'] ?? 'Not found',
                    'icon' => 'heroicon-o-cog',
                    'color' => 'green',
                ],
                [
                    'label' => 'Language',
                    'value' => $opengraphAnalysis['language'] ?? 'Not found',
                    'icon' => 'heroicon-o-language',
                    'color' => 'purple',
                ],
                [
                    'label' => 'Languages',
                    'value' => $opengraphAnalysis['languages'] ?? 'Not found',
                    'icon' => 'heroicon-o-language',
                    'color' => 'purple',
                ],
                [
                    'label' => 'Provider Name',
                    'value' => $opengraphAnalysis['provider_name'] ?? 'Not found',
                    'icon' => 'heroicon-o-globe-alt',
                    'color' => 'blue',
                ],
                [
                    'label' => 'Provider URL',
                    'value' => $opengraphAnalysis['provider_url'] ?? 'Not found',
                    'icon' => 'heroicon-o-link',
                    'color' => 'cyan',
                ],
                [
                    'label' => 'Icon',
                    'value' => '<img src="data:image/png;base64,' . base64_encode(file_get_contents($opengraphAnalysis['icon'])) . '" alt="Icon" class="w-10 h-10 rounded-full" />',
                    'icon' => 'heroicon-o-star',
                    'color' => 'yellow',
                ],
                [
                    'label' => 'Published Time',
                    'value' => $opengraphAnalysis['published_time'] ?? 'Not found',
                    'icon' => 'heroicon-o-calendar',
                    'color' => 'green',
                ],
                [
                    'label' => 'License',
                    'value' => $opengraphAnalysis['license'] ?? 'Not found',
                    'icon' => 'heroicon-o-document-text',
                    'color' => 'violet',
                ],
                [
                    'label' => 'Feeds',
                    'value' => $opengraphAnalysis['feeds'] ?? 'Not found',
                    'icon' => 'heroicon-o-rss',
                    'color' => 'orange',
                ],
            ];
            @endphp

            @foreach($metadataItems as $item)
                <div class="flex justify-between items-center p-3 bg-white/50 dark:bg-gray-800/50 rounded-lg hover:bg-white/80 dark:hover:bg-gray-800/80 transition-all duration-300 border-l-4 border-{{ $item['color'] }}-400">
                    <span class="text-gray-700 dark:text-gray-300 flex items-center font-medium">
                        <x-icon name="{{ $item['icon'] }}" class="w-5 h-5 mr-2 text-{{ $item['color'] }}-500" />
                        {{ $item['label'] }}
                    </span>
                    @if(is_array($item['value']))
                        <span class="text-gray-900 dark:text-gray-100 font-semibold">{{ implode(', ', $item['value']) }}</span>
                    @else
                        <span class="text-gray-900 dark:text-gray-100 font-semibold">{!! $item['value'] !!}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>