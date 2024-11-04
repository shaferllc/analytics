<?php

namespace Shaferllc\Analytics;

use Livewire\Livewire;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPackageTools\Package;
use Shaferllc\Analytics\Commands\AnalyticsCommand;

use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Shaferllc\Analytics\Http\View\Composers\UserStatsComposer;
use Shaferllc\Analytics\Http\View\Composers\UserWebsitesComposer;

class AnalyticsServiceProvider extends PackageServiceProvider
{

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
            'shaferllc.analytics.livewire.certificate' => \Shaferllc\Analytics\Livewire\Certificate::class,
            'shaferllc.analytics.livewire.events' => \Shaferllc\Analytics\Livewire\Events::class,
            'shaferllc.analytics.livewire.realtime' => \Shaferllc\Analytics\Livewire\Realtime::class,
            'shaferllc.analytics.livewire.analytics' => \Shaferllc\Analytics\Livewire\Analytics::class,
            'shaferllc.analytics.livewire.browsers' => \Shaferllc\Analytics\Livewire\Browsers::class,
            'shaferllc.analytics.livewire.screen-resolutions' => \Shaferllc\Analytics\Livewire\ScreenResolutions::class,
            // 'shaferllc.analytics.livewire.overview' => \Shaferllc\Analytics\Livewire\Overview::class,
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
            ]);
    }
}
