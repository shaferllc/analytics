<?php

use Streamline\Streamline;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Route;
use Shaferllc\Analytics\Models\Website;
use Shaferllc\Analytics\Http\Controllers\WebsiteController;

Route::prefix('sites/{site}/analytics')->middleware(Streamline::middleware())
    ->name('sites.analytics.')->group(function () {
        Route::get('/', \Shaferllc\Analytics\Livewire\Overview::class)->name('overview');
    // Route::get('/sites/create', [WebsiteController::class, 'create'])->name('sites.create');
    // Route::post('/sites/create', [WebsiteController::class, 'store']);

    // Route::prefix('sites/{website}')->group(function () {

        // Route::post('edit', [WebsiteController::class, 'update']);
        // Route::post('destroy', [WebsiteController::class, 'destroy'])->name('sites.destroy');
        // Route::get('edit', [WebsiteController::class, 'edit'])->name('sites.edit');

        $components = [
            'realtime' => \Shaferllc\Analytics\Livewire\Realtime::class,
            'events' => \Shaferllc\Analytics\Livewire\Events::class,
            'browsers' => \Shaferllc\Analytics\Livewire\Browsers::class,
            'devices' => \Shaferllc\Analytics\Livewire\Devices::class,
            'operating_systems' => \Shaferllc\Analytics\Livewire\OperatingSystems::class,
            'screen_resolutions' => \Shaferllc\Analytics\Livewire\ScreenResolutions::class,
            'languages' => \Shaferllc\Analytics\Livewire\Languages::class,
            'cities' => \Shaferllc\Analytics\Livewire\Cities::class,
            'countries' => \Shaferllc\Analytics\Livewire\Countries::class,
            'continents' => \Shaferllc\Analytics\Livewire\Continents::class,
            'campaigns' => \Shaferllc\Analytics\Livewire\Campaigns::class,
            'social_networks' => \Shaferllc\Analytics\Livewire\SocialNetworks::class,
            'search_engines' => \Shaferllc\Analytics\Livewire\SearchEngines::class,
            'referrers' => \Shaferllc\Analytics\Livewire\Referrers::class,
            'landing_pages' => \Shaferllc\Analytics\Livewire\LandingPages::class,
            'pages' => \Shaferllc\Analytics\Livewire\Pages::class,
            'real_time' => \Shaferllc\Analytics\Livewire\RealTime::class,
            'tracking-code' => \Shaferllc\Analytics\Livewire\TrackingCode::class,
            'technology' => \Shaferllc\Analytics\Livewire\Technology::class,
            // 'exit_pages' => \Shaferllc\Analytics\Livewire\ExitPages::class,
            'geographic' => \Shaferllc\Analytics\Livewire\Geographic::class,
            'timezones' => \Shaferllc\Analytics\Livewire\Timezones::class,
            'user-agents' => \Shaferllc\Analytics\Livewire\UserAgents::class,
            'acquisitions' => \Shaferllc\Analytics\Livewire\Acquisitions::class,
            'sessions' => \Shaferllc\Analytics\Livewire\Sessions::class,
            'debug' => \Shaferllc\Analytics\Livewire\Debug::class,
        ];
        foreach ($components as $name => $class) {
            Route::get("/{$name}/{value?}", $class)->name(Str::replace('_', '-', $name));
        }

        Route::get('/page/{value}', \Shaferllc\Analytics\Livewire\Page::class)->name('page');
    // });
})->scopeBindings();
