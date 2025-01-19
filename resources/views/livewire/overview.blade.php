<x-site :site="$site">

<div class="space-y-4">
        <x-analytics::breadcrumbs :breadcrumbs="[
            [
                'url' => route('sites.analytics.overview', ['site' => $site->id]),
                'label' => __('Dashboard'),
            ],
            [
                'url' => route('sites.analytics.acquisitions', ['site' => $site->id]),
                'label' => __('Acquisitions'),
            ],
            [
                'url' => route('sites.analytics.sessions', ['site' => $site->id]),
                'label' => __('Sessions'),
            ]
        ]" />
        @include('analytics::livewire.partials.nav')

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <x-analytics::stat-card
                    :title="__('Total Visitors')"
                    :value="number_format($totalVisitors)"
                    :oldValue="$totalVisitorsOld"
                    :icon="'heroicon-o-users'"
                    :color="'indigo'"
                />

                <x-analytics::stat-card
                    :title="__('Total Pageviews')"
                    :value="number_format($totalPageviews)"
                    :oldValue="$totalPageviewsOld"
                    :icon="'heroicon-o-document-text'"
                    :color="'indigo'"
                />

                <x-analytics::stat-card
                    :title="__('Unique Pages')"
                    :value="number_format($totalPages)"
                    :icon="'heroicon-o-document-duplicate'"
                    :color="'indigo'"
                />

                <x-analytics::stat-card
                    :title="__('Avg Session Duration')"
                    :value="gmdate('H:i:s', $avgSessionDuration)"
                    :icon="'heroicon-o-clock'"
                    :color="'indigo'"
                />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-analytics::chart-card :title="__('Visitors')" :data="$visitorsMap" />
                <x-analytics::chart-card :title="__('Pageviews')" :data="$pageviewsMap" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                <x-analytics::list-card
                    :title="__('Top Pages')"
                    :items="$pages"
                    :total="$totalPages"
                    :route="route('sites.analytics.pages', ['site' => $site->id])"
                />

                <x-analytics::list-card
                    :title="__('Top Referrers')"
                    :items="$referrers"
                    :total="$totalReferrers"
                    :route="route('sites.analytics.referrers', ['site' => $site->id])"
                />

                <x-analytics::list-card
                    :title="__('Top Countries')"
                    :items="$countries"
                    :route="route('sites.analytics.countries', ['site' => $site->id])"
                />

                <x-analytics::list-card
                    :title="__('Top Browsers')"
                    :items="$browsers"
                    :route="route('sites.analytics.browsers', ['site' => $site->id])"
                />

                <x-analytics::list-card
                    :title="__('Top Operating Systems')"
                    :items="$operatingSystems"
                    :route="route('sites.analytics.operating-systems', ['site' => $site->id])"
                />

                <x-analytics::list-card
                    :title="__('Top Events')"
                    :items="$events"
                    :route="route('sites.analytics.events', ['site' => $site->id])"
                />
            </div>
        </div>
    </div>
</x-website>
