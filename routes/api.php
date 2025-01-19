<?php

use Illuminate\Support\Facades\Route;
use Shaferllc\Analytics\Http\Controllers\API\EventController;
use Shaferllc\Analytics\Http\Controllers\API\TrackerController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::prefix('v1')->middleware('auth:api')->group(function () {
//     Route::apiResource('stats', 'API\StatController', ['parameters' => [
//         'stats' => 'id'
//     ], 'only' => ['show'], 'as' => 'api'])->middleware('api.guard');

//     Route::apiResource('websites', 'API\WebsiteController', ['parameters' => [
//         'websites' => 'id'
//     ], 'as' => 'api'])->middleware('api.guard');

//     Route::apiResource('account', 'API\AccountController', ['only' => [
//         'index'
//     ], 'as' => 'api'])->middleware('api.guard');

//     Route::fallback(function () {
//         return response()->json(['message' => __('Resource not found.'), 'status' => 404], 404);
//     });
// });
Route::prefix('api')->name('api.')->middleware('api')->group(function () {
    Route::prefix('v1')->name('v1.')->group(function () {
        Route::get('monitor', TrackerController::class)->name('monitor');
        Route::post('event', EventController::class)->name('event');
    });
});
