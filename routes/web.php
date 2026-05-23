<?php

use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\KerjasamaController;
use App\Http\Controllers\Public\StatistikController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MouController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\RenewalController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AttachmentController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FacultyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/kerjasama', [KerjasamaController::class, 'index'])->name('kerjasama.index');
Route::get('/kerjasama/{slug}', [KerjasamaController::class, 'show'])->name('kerjasama.show');
Route::get('/kerjasama/{slug}/pdf', [KerjasamaController::class, 'viewPdf'])->name('kerjasama.pdf');
Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');
Route::get('/tentang', [HomeController::class, 'tentang'])->name('tentang');

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware(\App\Http\Middleware\AdminAuth::class)->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // MoU CRUD
        Route::resource('mou', MouController::class);
        Route::post('/mou/{mou}/restore', [MouController::class, 'restore'])->name('mou.restore');
        Route::get('/mou-trashed', [MouController::class, 'trashed'])->name('mou.trashed');

        // Renewal
        Route::get('/mou/{mou}/renew', [RenewalController::class, 'create'])->name('renewal.create');
        Route::post('/mou/{mou}/renew', [RenewalController::class, 'store'])->name('renewal.store');
        Route::get('/mou/{mou}/renewals', [RenewalController::class, 'history'])->name('renewal.history');

        // Attachments
        Route::post('/mou/{mou}/attachments', [AttachmentController::class, 'store'])->name('attachments.store');
        Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
        Route::get('/attachments/{attachment}/download', [AttachmentController::class, 'download'])->name('attachments.download');

        // Institutions
        Route::resource('institutions', InstitutionController::class);
        Route::get('/api/institutions/search', [InstitutionController::class, 'search'])->name('institutions.search');

        // Categories
        Route::resource('categories', CategoryController::class)->except(['show']);

        // Faculties
        Route::resource('faculties', FacultyController::class)->except(['show']);

        // Import
        Route::get('/import', [ImportController::class, 'index'])->name('import.index');
        Route::post('/import/upload', [ImportController::class, 'upload'])->name('import.upload');
        Route::post('/import/process', [ImportController::class, 'process'])->name('import.process');
        Route::get('/import/template', [ImportController::class, 'downloadTemplate'])->name('import.template');
        Route::get('/import/logs', [ImportController::class, 'logs'])->name('import.logs');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');

        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

        // Activity Log
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');
    });
});
