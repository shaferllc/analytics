<x-site :site="$site">
    <div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('sites.analytics.overview', ['site' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('sites.analytics.tracking-code', ['site' => $site->id]),
                'label' => __('Tracking Code'),
                'icon' => 'heroicon-o-code-bracket',
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <x-analytics::title
            :title="__('Tracking Code')"
            :description="__('Add analytics tracking to your website.')"
            :icon="'heroicon-o-code-bracket'"
            :website="$site"
        />

        <div>
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-lg border border-gray-700 p-6">

                <div class="space-y-4">
                    @include('analytics::shared.message')
                    @include('analytics::shared.tracking-code')
                </div>
            </div>
        </div>
    </div>
</x-site>
