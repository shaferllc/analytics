<?php

use Streamline\Streamline;
use Illuminate\Support\Facades\Route;
use Shaferllc\Analytics\Http\Controllers\StatController;
use Shaferllc\Analytics\Http\Controllers\WebsiteController;
use Shaferllc\Analytics\Models\Website;

Route::prefix('analytics')->middleware(Streamline::middleware())->name('analytics.')->group(function () {
    Route::get('/', \Shaferllc\Analytics\Livewire\Analytics::class)->name('index');
    Route::get('/sites/create', [WebsiteController::class, 'create'])->name('sites.create'); 
    Route::post('/sites/create', [WebsiteController::class, 'store']);
  
    Route::prefix('sites/{website}')->group(function () {

        Route::post('edit', [WebsiteController::class, 'update']);
        Route::post('destroy', [WebsiteController::class, 'destroy'])->name('sites.destroy');
        Route::get('edit', [WebsiteController::class, 'edit'])->name('sites.edit'); 

        $components = [
            '/' => \Shaferllc\Analytics\Livewire\Overview::class,
            'overview' => \Shaferllc\Analytics\Livewire\Overview::class,
            'realtime' => \Shaferllc\Analytics\Livewire\Realtime::class,
            'events' => \Shaferllc\Analytics\Livewire\Events::class,
            'browsers' => \Shaferllc\Analytics\Livewire\Browsers::class,
            'devices' => \Shaferllc\Analytics\Livewire\Devices::class,
            'operating_systems' => \Shaferllc\Analytics\Livewire\OperatingSystems::class,
            'screen_resolutions' => \Shaferllc\Analytics\Livewire\ScreenResolutions::class,
            'languages' => \Shaferllc\Analytics\Livewire\Languages::class,
            'cities' => \Shaferllc\Analytics\Livewire\Cities::class,
            'dns' => \Shaferllc\Analytics\Livewire\Dns::class,
            'countries' => \Shaferllc\Analytics\Livewire\Countries::class,
            'continents' => \Shaferllc\Analytics\Livewire\Continents::class,
            'campaigns' => \Shaferllc\Analytics\Livewire\Campaigns::class,
            'social_networks' => \Shaferllc\Analytics\Livewire\SocialNetworks::class,
            'search_engines' => \Shaferllc\Analytics\Livewire\SearchEngines::class,
            'referrers' => \Shaferllc\Analytics\Livewire\Referrers::class,
            'landing_pages' => \Shaferllc\Analytics\Livewire\LandingPages::class,
            'pages' => \Shaferllc\Analytics\Livewire\Pages::class,
            'real_time' => \Shaferllc\Analytics\Livewire\RealTime::class,
            'tracking_code' => \Shaferllc\Analytics\Livewire\TrackingCode::class,
            // 'exit_pages' => \Shaferllc\Analytics\Livewire\ExitPages::class,
            
        ];
        foreach ($components as $name => $class) {
            Route::get("/{$name}", $class)->name("stats.{$name}");
        }

        Route::post('/password', [StatController::class, 'validatePassword'])->name('stats.password');
    });
})->scopeBindings();
