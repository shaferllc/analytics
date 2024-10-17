<?php

namespace ShaferLLC\Analytics;

use Illuminate\Support\Facades\View;
use Spatie\LaravelPackageTools\Package;
use ShaferLLC\Analytics\Commands\AnalyticsCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use ShaferLLC\Analytics\Http\View\Composers\UserStatsComposer;
use ShaferLLC\Analytics\Http\View\Composers\UserWebsitesComposer;

class AnalyticsServiceProvider extends PackageServiceProvider
{

    public function boot()
    {
        // View::composer([
        //     'shared.sidebars.user'
        // ], UserWebsitesComposer::class);

        // View::composer([
        //     'shared.header',
        // ], UserStatsComposer::class);

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
            ->runsMigrations()

            ->hasMigrations([
                'create_cronjobs_table',
                'create_languages_table',
                'create_websites_table',
                'create_stats_table',
                'create_recents_table',
            ])
            ->hasCommand(AnalyticsCommand::class);
           
    }
}
