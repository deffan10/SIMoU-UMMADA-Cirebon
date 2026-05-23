<?php

use App\Http\Controllers\Api\PublicApiController;
use App\Http\Controllers\Api\DashboardApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->name('api.')->group(function () {
    // Public endpoints
    Route::get('/mous', [PublicApiController::class, 'index'])->name('mous.index');
    Route::get('/mous/{id}', [PublicApiController::class, 'show'])->name('mous.show');
    Route::get('/institutions', [PublicApiController::class, 'institutions'])->name('institutions');
    Route::get('/categories', [PublicApiController::class, 'categories'])->name('categories');
    Route::get('/statistics', [PublicApiController::class, 'statistics'])->name('statistics');
    Route::get('/statistics/yearly', [PublicApiController::class, 'yearlyStatistics'])->name('statistics.yearly');
    Route::get('/mous/{id}/renewals', [PublicApiController::class, 'renewals'])->name('mous.renewals');

    // Dashboard summary (protected by admin auth)
    Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {
        Route::get('/dashboard/summary', [DashboardApiController::class, 'summary'])->name('dashboard.summary');
        Route::get('/dashboard/chart-data', [DashboardApiController::class, 'chartData'])->name('dashboard.chart');
    });
});
