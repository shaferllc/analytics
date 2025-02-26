<?php

namespace Shaferllc\Analytics;

use Livewire\Livewire;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPackageTools\Package;
use Shaferllc\Analytics\Pipelines\CreatePage;
use Shaferllc\Analytics\Commands\CrawlCommand;
use Shaferllc\Analytics\Commands\ReportCommand;
use Shaferllc\Analytics\Pipelines\CreateVisitor;
use Shaferllc\Analytics\Pipelines\UpdatePageMeta;
use Shaferllc\Analytics\Commands\AnalyticsCommand;
use Shaferllc\Analytics\Pipelines\ProcessPageData;
use Shaferllc\Analytics\Pipelines\HandleSessionEnd;
use Shaferllc\Analytics\Pipelines\ProcessDeviceData;
use Shaferllc\Analytics\Pipelines\HandleSessionStart;
use Shaferllc\Analytics\Pipelines\ProcessBrowserData;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Shaferllc\Analytics\Pipelines\ProcessViewportChanges;
use Shaferllc\Analytics\Pipelines\ProcessTrafficSourceData;
use Shaferllc\Analytics\Pipelines\ProcessUserInteractionData;
use Shaferllc\Analytics\Http\View\Composers\UserStatsComposer;
use Shaferllc\Analytics\Pipelines\ProcessPerformanceMetricsData;
use Shaferllc\Analytics\Http\View\Composers\UserWebsitesComposer;

class AnalyticsServiceProvider extends PackageServiceProvider
{

    public function register()
    {
        parent::register();

        $this->app->bind(CreatePage::class);
        $this->app->bind(UpdatePageMeta::class);
        $this->app->bind(CreateVisitor::class);
        $this->app->bind(HandleSessionStart::class);
        $this->app->bind(HandleSessionEnd::class);
        $this->app->bind(ProcessViewportChanges::class);
        $this->app->bind(ProcessPageData::class);
        $this->app->bind(ProcessTrafficSourceData::class);
        $this->app->bind(ProcessBrowserData::class);
        $this->app->bind(ProcessPerformanceMetricsData::class);
        $this->app->bind(ProcessUserInteractionData::class);
        $this->app->bind(ProcessDeviceData::class);
    }

    public function boot()
    {
        parent::boot();
        View::composer([
            'analytics::shared.sidebars.user'
        ], UserWebsitesComposer::class);

        View::composer([
            'analytics::shared.header',
        ], UserStatsComposer::class);

        $components = [
            // 'shaferllc.analytics.livewire.certificate' => \Shaferllc\Analytics\Livewire\Certificate::class,
            'shaferllc.analytics.livewire.events' => \Shaferllc\Analytics\Livewire\Events::class,
            'shaferllc.analytics.livewire.realtime' => \Shaferllc\Analytics\Livewire\Realtime::class,
            // 'shaferllc.analytics.livewire.analytics' => \Shaferllc\Analytics\Livewire\Analytics::class,
            'shaferllc.analytics.livewire.browsers' => \Shaferllc\Analytics\Livewire\Browsers::class,
            'shaferllc.analytics.livewire.screen-resolutions' => \Shaferllc\Analytics\Livewire\ScreenResolutions::class,
            'shaferllc.analytics.livewire.overview' => \Shaferllc\Analytics\Livewire\Overview::class,
            // 'shaferllc.analytics.livewire.password' => \Shaferllc\Analytics\Livewire\Password::class,
            'shaferllc.analytics.livewire.cities' => \Shaferllc\Analytics\Livewire\Cities::class,
            'shaferllc.analytics.livewire.countries' => \Shaferllc\Analytics\Livewire\Countries::class,
            'shaferllc.analytics.livewire.continents' => \Shaferllc\Analytics\Livewire\Continents::class,
            'shaferllc.analytics.livewire.campaigns' => \Shaferllc\Analytics\Livewire\Campaigns::class,
            'shaferllc.analytics.livewire.social_networks' => \Shaferllc\Analytics\Livewire\SocialNetworks::class,
            'shaferllc.analytics.livewire.search_engines' => \Shaferllc\Analytics\Livewire\SearchEngines::class,
            'shaferllc.analytics.livewire.referrers' => \Shaferllc\Analytics\Livewire\Referrers::class,
            'shaferllc.analytics.livewire.landing_pages' => \Shaferllc\Analytics\Livewire\LandingPages::class,
            'shaferllc.analytics.livewire.pages' => \Shaferllc\Analytics\Livewire\Pages::class,
            'shaferllc.analytics.livewire.real_time' => \Shaferllc\Analytics\Livewire\RealTime::class,
            'shaferllc.analytics.livewire.user_agents' => \Shaferllc\Analytics\Livewire\UserAgents::class,
            'shaferllc.analytics.livewire.devices' => \Shaferllc\Analytics\Livewire\Devices::class,
            'shaferllc.analytics.livewire.operating_systems' => \Shaferllc\Analytics\Livewire\OperatingSystems::class,
            'shaferllc.analytics.livewire.timezones' => \Shaferllc\Analytics\Livewire\Timezones::class,
            'shaferllc.analytics.livewire.languages' => \Shaferllc\Analytics\Livewire\Languages::class,
            'shaferllc.analytics.livewire.sessions' => \Shaferllc\Analytics\Livewire\Sessions::class,
            'shaferllc.analytics.livewire.page' => \Shaferllc\Analytics\Livewire\Page::class,
            'shaferllc.analytics.livewire.debug' => \Shaferllc\Analytics\Livewire\Debug::class,
            // 'shaferllc.analytics.livewire.exit_pages' => \Shaferllc\Analytics\Livewire\ExitPages::class,
        ];

        foreach ($components as $name => $class) {
            Livewire::component($name, $class);
        }
    }

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('analytics')
            ->hasConfigFile()
            ->hasViews('analytics')
            ->hasRoute('web')
            ->hasRoute('api')
            ->hasAssets()
            ->runsMigrations()

            ->hasMigrations([
                'create_cronjobs_table',
                'create_languages_table',
                'create_websites_table',
                'create_stats_table',
                'create_recents_table',
            ])
            ->hasCommands([
                AnalyticsCommand::class,
                ReportCommand::class,
                CrawlCommand::class,
            ]);
    }
}
