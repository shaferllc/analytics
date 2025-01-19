<div x-show="activeTab === 'internationalization'">
    <h3 class="font-bold mb-4 flex items-center gap-2">
        <x-icon name="heroicon-o-globe-alt" class="w-5 h-5 text-indigo-400" />
        <span>Internationalization Analysis</span>
    </h3>
    
    <div class="space-y-6">
        @include('analytics::livewire.partials.page.reports.language_detection')
        @include('analytics::livewire.partials.page.reports.character_encoding')
        @include('analytics::livewire.partials.page.reports.text_direction')
        {{-- @include('analytics::livewire.partials.page.reports.content_localization') --}}
        {{-- @include('analytics::livewire.partials.page.reports.date_formats') --}}
        {{-- @include('analytics::livewire.partials.page.reports.number_formats') --}}
        {{-- @include('analytics::livewire.partials.page.reports.translation_readiness') --}}
        @include('analytics::livewire.partials.page.reports.language')
        {{-- @include('analytics::livewire.partials.page.reports.content') --}}
    </div>
</div>
