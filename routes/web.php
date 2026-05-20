<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminInquiryController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicReportController;
use App\Http\Controllers\StripeWebhookController;
use App\Support\LocalizedUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicReportController::class, 'landing'])->name('home');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::get('/get-report', [PublicReportController::class, 'index'])->name('get-report');
Route::get('/submit-url', [PublicReportController::class, 'showUrlForm'])->name('submit-url');
Route::get('/submit-email', [PublicReportController::class, 'showEmailForm'])->name('submit-email');
Route::get('/report/{pageToken}', [PublicReportController::class, 'status'])->name('report.status');
Route::get('/checkout/success/{pageToken}', [PublicReportController::class, 'paymentSuccess'])->name('checkout.success');
Route::get('/checkout/cancel/{pageToken}', [PublicReportController::class, 'paymentCancel'])->name('checkout.cancel');
Route::get('/privacy-policy', [PublicReportController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/terms-and-conditions', [PublicReportController::class, 'termsAndConditions'])->name('terms-and-conditions');
Route::get('/cookie-policy', [PublicReportController::class, 'cookiePolicy'])->name('cookie-policy');
Route::get('/politica-de-confidentialitate', [PublicReportController::class, 'privacyPolicy']);
Route::get('/termeni-si-conditii', [PublicReportController::class, 'termsAndConditions']);
Route::get('/politica-de-cookie-uri', [PublicReportController::class, 'cookiePolicy']);

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/validate-url', [PublicReportController::class, 'validateUrl'])->name('validate-url');
    Route::post('/submit-email', [PublicReportController::class, 'submitEmail'])->name('submit-email.store');
    Route::post('/report/{pageToken}/checkout', [PublicReportController::class, 'retryCheckout'])->name('report.checkout');
});

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->name('stripe.webhook');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/locale-test', function (Request $request) {
    $host = LocalizedUrl::requestHost($request);

    return response()->json([
        'host' => $host,
        'locale' => app()->getLocale(),
        'message' => __('get_report'),
    ]);
})->name('locale.test');

Route::get('/sitemap.xml', function () {
    abort_unless(config('seo.indexing'), 404);

    $paths = ['/', '/contact', '/get-report', '/privacy-policy', '/terms-and-conditions', '/cookie-policy'];
    $urls = collect(config('locales.supported', []))
        ->flatMap(fn (string $locale) => collect($paths)->map(fn (string $path) => LocalizedUrl::urlForLocale($locale, $path)))
        ->all();

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

// API endpoint for polling (no locale needed)
Route::get('/api/report-status/{page_token}', [PublicReportController::class, 'statusJson']);

// Admin routes
Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/inquiries', [AdminInquiryController::class, 'index'])->name('admin.inquiries');
    Route::get('/reports/{id}', [AdminController::class, 'show'])->name('admin.reports.show');
    Route::post('/reports/{id}/send', [AdminController::class, 'send'])->name('admin.reports.send');
    Route::get('/settings', [AdminSettingsController::class, 'show'])->name('admin.settings');
    Route::post('/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::get('/test-pdf', [AdminSettingsController::class, 'testPdf'])->name('admin.test-pdf');
});

require __DIR__.'/auth.php';
