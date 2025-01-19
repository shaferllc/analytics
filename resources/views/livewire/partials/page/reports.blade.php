<div x-show="activeTab === 'reports'" x-data="{ showModal: false, selectedReport: null }">
    @foreach($reports as $report)
        <div class="bg-indigo-900/10 border border-indigo-600 rounded-xl p-6 mb-6 hover:bg-indigo-900/20 transition-all duration-300 cursor-pointer shadow-lg hover:shadow-indigo-900/20 group" @click="showModal = true; selectedReport = '{{ $report->id }}'">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600/20 p-2 rounded-lg group-hover:bg-indigo-600/30 transition-colors">
                        <x-icon name="heroicon-o-document-text" class="w-6 h-6 text-indigo-400" />
                    </div>
                    <h3 class="text-xl font-bold text-indigo-100">{{ $report->created_at->format('d M Y H:i') }}</h3>
                </div>
                <span class="text-indigo-400 flex items-center gap-2 group-hover:translate-x-1 transition-transform">
                    <span>View full report</span>
                    <x-icon name="heroicon-o-arrow-right" class="w-4 h-4" />
                </span>
            </div>
            <div class="mt-6 grid grid-cols-3 gap-8">
                <div class="relative">
                    <div class="text-sm font-medium text-indigo-400 mb-1 flex items-center gap-2">
                        <x-icon name="heroicon-o-magnifying-glass" class="w-4 h-4" />
                        <span>SEO Score</span>
                    </div>
                    <div class="text-2xl font-bold text-indigo-100">{{ isset($report->seo_score) ? $report->seo_score : 0 }}%</div>
                    <div class="absolute right-0 top-0 h-full w-px bg-gradient-to-b from-transparent via-indigo-600/50 to-transparent"></div>
                </div>
                <div class="relative">
                    <div class="text-sm font-medium text-indigo-400 mb-1 flex items-center gap-2">
                        <x-icon name="heroicon-o-bolt" class="w-4 h-4" />
                        <span>Performance Score</span>
                    </div>
                    <div class="text-2xl font-bold text-indigo-100">{{ isset($report->performance_score) ? $report->performance_score : 0 }}%</div>
                    <div class="absolute right-0 top-0 h-full w-px bg-gradient-to-b from-transparent via-indigo-600/50 to-transparent"></div>
                </div>
                <div>
                    <div class="text-sm font-medium text-indigo-400 mb-1 flex items-center gap-2">
                        <x-icon name="heroicon-o-exclamation-triangle" class="w-4 h-4" />
                        <span>Issues Found</span>
                    </div>
                    <div class="text-2xl font-bold text-indigo-100">{{ isset($report->issues) ? count($report->issues) : 0 }}</div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-indigo-950 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
                <div class="bg-indigo-950 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    @foreach($reports as $report)
                        <div x-show="selectedReport === '{{ $report->id }}'">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-2xl font-bold">Report from {{ $report->created_at->format('d M Y') }}</h2>
                                <button @click="showModal = false" class="text-indigo-300 hover:text-indigo-100">
                                    <x-icon name="heroicon-o-x-mark" class="w-6 h-6" />
                                </button>
                            </div>
                            <div class="space-y-6">
                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-indigo-900/50 p-4 rounded-lg">
                                        <h3 class="font-bold mb-2">SEO Analysis</h3>
                                        <div class="text-3xl font-bold">{{ isset($report->seo_score) ? $report->seo_score : 0 }}%</div>
                                    </div>
                                    <div class="bg-indigo-900/50 p-4 rounded-lg">
                                        <h3 class="font-bold mb-2">Performance</h3>
                                        <div class="text-3xl font-bold">{{ isset($report->performance_score) ? $report->performance_score : 0 }}%</div>
                                    </div>
                                    <div class="bg-indigo-900/50 p-4 rounded-lg">
                                        <h3 class="font-bold mb-2">Issues</h3>
                                        <div class="text-3xl font-bold">{{ isset($report->issues) ? count($report->issues) : 0 }}</div>
                                    </div>
                                </div>

                                <div>
                                    <div x-data="{ activeTab: 'internationalization' }" class="space-y-4">
                                        <div class="border-b border-indigo-800">
                                            <nav class="flex space-x-8" aria-label="Tabs">
                                                @php
                                                $tabs = [
                                                    'internationalization' => [
                                                        'icon' => 'heroicon-o-globe-alt',
                                                        'label' => 'Internationalization'
                                                    ],
                                                    'seo' => [
                                                        'icon' => 'heroicon-o-magnifying-glass',
                                                        'label' => 'SEO Analysis'
                                                    ],
                                                    'performance' => [
                                                        'icon' => 'heroicon-o-bolt',
                                                        'label' => 'Performance'
                                                    ]
                                                ];
                                                @endphp

                                                @foreach($tabs as $id => $tab)
                                                    <button 
                                                        @click="activeTab = '{{ $id }}'"
                                                        :class="{ 'border-indigo-500 text-indigo-300': activeTab === '{{ $id }}', 'border-transparent text-indigo-500 hover:text-indigo-400 hover:border-indigo-400': activeTab !== '{{ $id }}' }"
                                                        class="flex items-center gap-2 py-4 px-1 border-b-2 font-medium text-sm"
                                                    >
                                                        <x-icon name="{{ $tab['icon'] }}" class="w-5 h-5" />
                                                        {{ $tab['label'] }}
                                                    </button>
                                                @endforeach
                                            </nav>
                                        </div>

                                        @include('analytics::livewire.partials.page.reports.internationalization')

                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
