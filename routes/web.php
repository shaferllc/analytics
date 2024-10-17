<?php

use Illuminate\Support\Facades\Route;
use Streamline\Streamline;
use ShaferLLC\Analytics\Http\Controllers\AnalyticsController;
use ShaferLLC\Analytics\Http\Controllers\WebsiteController;
use ShaferLLC\Analytics\Http\Controllers\AdminController;
use ShaferLLC\Analytics\Http\Controllers\CronjobController;
use ShaferLLC\Analytics\Http\Controllers\WebhookController;
use ShaferLLC\Analytics\Http\Controllers\DeveloperController;
use ShaferLLC\Analytics\Http\Controllers\StatController;


/* The above code is a commented-out PHP code block that appears to be related to routing in a Laravel
application. It seems to be using Laravel's Route facade to define a group of routes that are
wrapped in a middleware provided by a Streamline class. However, the code is currently commented out
and not active in the application. */
Route::middleware(Streamline::middleware())->group(function () {
    
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');

    // Route::get('/websites/create', [WebsiteController::class, 'create'])->name('websites.new');
    // Route::get('/websites/{id}/edit', [WebsiteController::class, 'edit'])->name('websites.edit');
    // Route::post('/websites/create', [WebsiteController::class, 'store']);
    // Route::post('/websites/{id}/edit', [WebsiteController::class, 'update']);
    // Route::post('/websites/{id}/destroy', [WebsiteController::class, 'destroy'])->name('websites.destroy');
});

// // Admin routes
// Route::prefix('admin')->middleware('admin')->group(function () {
//     Route::get('/license', [AdminController::class, 'license'])->name('admin.settings', 'license');
//     Route::post('/license', [AdminController::class, 'updateLicense']);

//     Route::middleware('license')->group(function () {
//         Route::redirect('/', 'admin/dashboard');
//         Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
//         Route::get('/settings/{id}', [AdminController::class, 'settings'])->name('admin.settings');
//         Route::post('/settings/{id}', [AdminController::class, 'updateSetting']);
        
//         // Users
//         Route::prefix('users')->group(function () {
//             Route::get('/', [AdminController::class, 'indexUsers'])->name('admin.users');
//             Route::get('/new', [AdminController::class, 'createUser'])->name('admin.users.new');
//             Route::get('/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
//             Route::post('/new', [AdminController::class, 'storeUser']);
//             Route::post('/{id}/edit', [AdminController::class, 'updateUser']);
//             Route::post('/{id}/destroy', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
//             Route::post('/{id}/disable', [AdminController::class, 'disableUser'])->name('admin.users.disable');
//             Route::post('/{id}/restore', [AdminController::class, 'restoreUser'])->name('admin.users.restore');
//         });

//         // Pages
//         Route::prefix('pages')->group(function () {
//             Route::get('/', [AdminController::class, 'indexPages'])->name('admin.pages');
//             Route::get('/new', [AdminController::class, 'createPage'])->name('admin.pages.new');
//             Route::get('/{id}/edit', [AdminController::class, 'editPage'])->name('admin.pages.edit');
//             Route::post('/new', [AdminController::class, 'storePage']);
//             Route::post('/{id}/edit', [AdminController::class, 'updatePage']);
//             Route::post('/{id}/destroy', [AdminController::class, 'destroyPage'])->name('admin.pages.destroy');
//         });

//         // Payments
//         Route::prefix('payments')->group(function () {
//             Route::get('/', [AdminController::class, 'indexPayments'])->name('admin.payments');
//             Route::get('/{id}/edit', [AdminController::class, 'editPayment'])->name('admin.payments.edit');
//             Route::post('/{id}/approve', [AdminController::class, 'approvePayment'])->name('admin.payments.approve');
//             Route::post('/{id}/cancel', [AdminController::class, 'cancelPayment'])->name('admin.payments.cancel');
//         });

//         Route::get('/invoices/{id}', [AdminController::class, 'showInvoice'])->name('admin.invoices.show');

//         // Plans
//         Route::prefix('plans')->group(function () {
//             Route::get('/', [AdminController::class, 'indexPlans'])->name('admin.plans');
//             Route::get('/new', [AdminController::class, 'createPlan'])->name('admin.plans.new');
//             Route::get('/{id}/edit', [AdminController::class, 'editPlan'])->name('admin.plans.edit');
//             Route::post('/new', [AdminController::class, 'storePlan']);
//             Route::post('/{id}/edit', [AdminController::class, 'updatePlan']);
//             Route::post('/{id}/disable', [AdminController::class, 'disablePlan'])->name('admin.plans.disable');
//             Route::post('/{id}/restore', [AdminController::class, 'restorePlan'])->name('admin.plans.restore');
//         });

//         // Coupons
//         Route::prefix('coupons')->group(function () {
//             Route::get('/', [AdminController::class, 'indexCoupons'])->name('admin.coupons');
//             Route::get('/new', [AdminController::class, 'createCoupon'])->name('admin.coupons.new');
//             Route::get('/{id}/edit', [AdminController::class, 'editCoupon'])->name('admin.coupons.edit');
//             Route::post('/new', [AdminController::class, 'storeCoupon']);
//             Route::post('/{id}/edit', [AdminController::class, 'updateCoupon']);
//             Route::post('/{id}/disable', [AdminController::class, 'disableCoupon'])->name('admin.coupons.disable');
//             Route::post('/{id}/restore', [AdminController::class, 'restoreCoupon'])->name('admin.coupons.restore');
//         });

//         // Tax Rates
//         Route::prefix('tax-rates')->group(function () {
//             Route::get('/', [AdminController::class, 'indexTaxRates'])->name('admin.tax_rates');
//             Route::get('/new', [AdminController::class, 'createTaxRate'])->name('admin.tax_rates.new');
//             Route::get('/{id}/edit', [AdminController::class, 'editTaxRate'])->name('admin.tax_rates.edit');
//             Route::post('/new', [AdminController::class, 'storeTaxRate']);
//             Route::post('/{id}/edit', [AdminController::class, 'updateTaxRate']);
//             Route::post('/{id}/disable', [AdminController::class, 'disableTaxRate'])->name('admin.tax_rates.disable');
//             Route::post('/{id}/restore', [AdminController::class, 'restoreTaxRate'])->name('admin.tax_rates.restore');
//         });

//         // Websites
//         Route::prefix('websites')->group(function () {
//             Route::get('/', [AdminController::class, 'indexWebsites'])->name('admin.websites');
//             Route::get('/{id}/edit', [AdminController::class, 'editWebsite'])->name('admin.websites.edit');
//             Route::post('/{id}/edit', [AdminController::class, 'updateWebsite']);
//             Route::post('/{id}/destroy', [AdminController::class, 'destroyWebsite'])->name('admin.websites.destroy');
//         });
//     });
// });


// // Cronjob routes
// Route::get('/cronjob', [CronjobController::class, 'index'])->name('cronjob');

// // Webhook routes
// Route::post('webhooks/paypal', [WebhookController::class, 'paypal'])->name('webhooks.paypal');
// Route::post('webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');
// Route::post('webhooks/razorpay', [WebhookController::class, 'razorpay'])->name('webhooks.razorpay');
// Route::post('webhooks/paystack', [WebhookController::class, 'paystack'])->name('webhooks.paystack');
// Route::post('webhooks/cryptocom', [WebhookController::class, 'cryptocom'])->name('webhooks.cryptocom');
// Route::post('webhooks/coinbase', [WebhookController::class, 'coinbase'])->name('webhooks.coinbase');

// // Developer routes
// Route::prefix('/developers')->group(function () {
//     Route::get('/', [DeveloperController::class, 'index'])->name('developers');
//     Route::get('/stats', [DeveloperController::class, 'stats'])->name('developers.stats');
//     Route::get('/websites', [DeveloperController::class, 'websites'])->name('developers.websites');
//     Route::get('/account', [DeveloperController::class, 'account'])->name('developers.account');
// });

// // Stat routes
// Route::prefix('/{id}')->group(function () {
//     Route::get('/', [StatController::class, 'index'])->name('stats.overview');
//     Route::get('/realtime', [StatController::class, 'realTime'])->name('stats.realtime');
//     Route::get('/pages', [StatController::class, 'pages'])->name('stats.pages');
//     Route::get('/landing_pages', [StatController::class, 'landingPages'])->name('stats.landing_pages');
//     Route::get('/referrers', [StatController::class, 'referrers'])->name('stats.referrers');
//     Route::get('/search-engines', [StatController::class, 'searchEngines'])->name('stats.search_engines');
//     Route::get('/social-networks', [StatController::class, 'socialNetworks'])->name('stats.social_networks');
//     Route::get('/campaigns', [StatController::class, 'campaigns'])->name('stats.campaigns');
//     Route::get('/continents', [StatController::class, 'continents'])->name('stats.continents');
//     Route::get('/countries', [StatController::class, 'countries'])->name('stats.countries');
//     Route::get('/cities', [StatController::class, 'cities'])->name('stats.cities');
//     Route::get('/languages', [StatController::class, 'languages'])->name('stats.languages');
//     Route::get('/browsers', [StatController::class, 'browsers'])->name('stats.browsers');
//     Route::get('/operating-systems', [StatController::class, 'operatingSystems'])->name('stats.operating_systems');
//     Route::get('/screen-resolutions', [StatController::class, 'screenResolutions'])->name('stats.screen_resolutions');
//     Route::get('/devices', [StatController::class, 'devices'])->name('stats.devices');
//     Route::get('/events', [StatController::class, 'events'])->name('stats.events');

//     Route::prefix('/export')->group(function () {
//         Route::get('/pages', [StatController::class, 'exportPages'])->name('stats.export.pages');
//         Route::get('/landing_pages', [StatController::class, 'exportLandingPages'])->name('stats.export.landing_pages');
//         Route::get('/referrers', [StatController::class, 'exportReferrers'])->name('stats.export.referrers');
//         Route::get('/search-engines', [StatController::class, 'exportSearchEngines'])->name('stats.export.search_engines');
//         Route::get('/social-networks', [StatController::class, 'exportSocialNetworks'])->name('stats.export.social_networks');
//         Route::get('/campaigns', [StatController::class, 'exportCampaigns'])->name('stats.export.campaigns');
//         Route::get('/continents', [StatController::class, 'exportContinents'])->name('stats.export.continents');
//         Route::get('/countries', [StatController::class, 'exportCountries'])->name('stats.export.countries');
//         Route::get('/cities', [StatController::class, 'exportCities'])->name('stats.export.cities');
//         Route::get('/languages', [StatController::class, 'exportLanguages'])->name('stats.export.languages');
//         Route::get('/browsers', [StatController::class, 'exportBrowsers'])->name('stats.export.browsers');
//         Route::get('/operating-systems', [StatController::class, 'exportOperatingSystems'])->name('stats.export.operating_systems');
//         Route::get('/screen-resolutions', [StatController::class, 'exportScreenResolutions'])->name('stats.export.screen_resolutions');
//         Route::get('/devices', [StatController::class, 'exportDevices'])->name('stats.export.devices');
//         Route::get('/events', [StatController::class, 'exportEvents'])->name('stats.export.events');
//     });

//     Route::post('/password', [StatController::class, 'validatePassword'])->name('stats.password');
// });
