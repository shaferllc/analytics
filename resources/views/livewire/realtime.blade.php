<x-site :site="$site">
    <div class="space-y-4" wire:poll.5s>
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('sites.analytics.overview', ['site' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('sites.analytics.realtime', ['site' => $site->id]),
                'label' => __('Realtime'),
                'icon' => 'heroicon-o-chart-bar',
            ]
        ]" />

        @include('analytics::livewire.partials.nav')


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2">
                <x-analytics::chart-card
                    title="{{ __('Active Visitors') }}"
                    :data="['Now' => $activeVisitors]"
                />
            </div>

            <div>
                <x-analytics::stat-card
                    title="{{ __('Page Views') }}"
                    :value="$pageviews"
                    icon="heroicon-o-document-text"
                />
            </div>

            <div>
                <x-analytics::list-card
                    title="{{ __('Top Pages') }}"
                    :items="$pages"
                />
            </div>

            <div>
                <x-analytics::list-card
                    title="{{ __('Countries') }}"
                    :items="$countries"
                />
            </div>

            <div>
                <x-analytics::list-card
                    title="{{ __('Referrers') }}"
                    :items="$referrers"
                />
            </div>
        </div>
    </div>

</x-website>
