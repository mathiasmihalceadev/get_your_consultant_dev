<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\PublicReportController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

// Redirect root to default locale
Route::get('/', fn () => redirect('/en'));

// Public routes with locale prefix
Route::prefix('{locale}')->where(['locale' => 'en|ro'])->middleware(SetLocale::class)->group(function () {
    Route::get('/', [PublicReportController::class, 'index'])->name('home');
    Route::get('/submit-url', [PublicReportController::class, 'showUrlForm'])->name('submit-url');
    Route::get('/submit-email', [PublicReportController::class, 'showEmailForm'])->name('submit-email');
    Route::get('/report/{page_token}', [PublicReportController::class, 'status'])->name('report.status');

    // Rate-limited public POST routes
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/validate-url', [PublicReportController::class, 'validateUrl'])->name('validate-url');
        Route::post('/submit-email', [PublicReportController::class, 'submitEmail'])->name('submit-email.store');
    });
});

// API endpoint for polling (no locale needed)
Route::get('/api/report-status/{page_token}', [PublicReportController::class, 'statusJson']);

// Admin routes
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/reports/{id}', [AdminController::class, 'show'])->name('admin.reports.show');
    Route::post('/reports/{id}/send', [AdminController::class, 'send'])->name('admin.reports.send');
    Route::get('/settings', [AdminSettingsController::class, 'show'])->name('admin.settings');
    Route::post('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
});

require __DIR__.'/auth.php';
