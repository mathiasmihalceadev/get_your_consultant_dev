<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\OpenAIService;
use App\Services\ReportPricingService;
use App\Services\StripeCheckoutService;
use App\Support\LocalizedUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class PublicReportController extends Controller
{
    public function landing(Request $request, ReportPricingService $pricing)
    {
        $locale = app()->getLocale();
        $alternates = collect(LocalizedUrl::publicLocales())
            ->mapWithKeys(fn (string $publicLocale) => [
                $publicLocale === 'ro' ? 'ro-RO' : 'en-US' => LocalizedUrl::publicUrlForLocale($publicLocale, '/'),
            ])
            ->all();

        return response()->view('public.landing', [
            'pricingCatalog' => $pricing->catalogForRequest(app()->getLocale(), $request),
            'canonical' => LocalizedUrl::publicUrlForLocale($locale, '/'),
            'alternates' => $alternates,
            'xDefault' => LocalizedUrl::publicUrlForLocale(LocalizedUrl::publicXDefaultLocale(), '/'),
        ]);
    }

    public function index()
    {
        return Inertia::render('Public/Index');
    }

    public function privacyPolicy()
    {
        return $this->renderLegalDocument('privacy-policy');
    }

    public function termsAndConditions()
    {
        return $this->renderLegalDocument('terms-and-conditions');
    }

    public function cookiePolicy()
    {
        return $this->renderLegalDocument('cookie-policy');
    }

    public function showUrlForm(Request $request)
    {
        if ($this->publicWizardMaintenanceEnabled()) {
            return redirect()->route('get-report');
        }

        $type = $request->query('type');

        if (!in_array($type, ['rental_living', 'buying_living'])) {
            return redirect()->route('home');
        }

        return Inertia::render('Public/SubmitUrl', [
            'reportType' => $type,
        ]);
    }

    public function validateUrl(Request $request, OpenAIService $openAI)
    {
        if ($this->publicWizardMaintenanceEnabled()) {
            return redirect()->route('get-report');
        }

        $locale = app()->getLocale();
        $validated = $request->validate(
            [
                'url' => ['required', 'url'],
                'report_type' => ['required', 'in:rental_living,buying_living'],
            ],
            [
                'url.required' => __('wizard_url_required'),
                'url.url' => __('wizard_url_invalid'),
            ],
        );

        $report = Report::create([
            'url' => $validated['url'],
            'report_type' => $validated['report_type'],
            'locale' => $locale,
            'status' => 'pending',
        ]);

        Log::channel('report')->info('URL submitted for validation', [
            'report_id' => $report->id,
            'type' => $report->report_type,
            'url' => $report->url,
        ]);

        try {
            $result = $openAI->validateUrl($validated['url'], $validated['report_type']);
        } catch (\Exception $e) {
            $message = __('wizard_url_validation_failed');

            $report->update([
                'status' => 'not_accessible',
                'error_message' => $message,
            ]);

            Log::channel('report')->error('URL validation exception', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'url' => $message,
            ]);
        }

        if (!$result['success']) {
            $message = $this->resolveUrlValidationMessage($result['reason_code'] ?? null);

            $report->update([
                'status' => 'not_accessible',
                'error_message' => $message,
            ]);

            Log::channel('report')->info('URL validation failed', [
                'report_id' => $report->id,
                'reason_code' => $result['reason_code'] ?? 'unknown',
                'reason' => $result['message'],
            ]);

            return back()->withErrors([
                'url' => $message,
            ]);
        }

        session(['report_id' => $report->id]);

        Log::channel('report')->info('URL validation passed', [
            'report_id' => $report->id,
        ]);

        return redirect()->route('submit-email');
    }

    public function showEmailForm()
    {
        $reportId = session('report_id');

        if (!$reportId) {
            return redirect()->route('home');
        }

        $report = Report::find($reportId);

        if (!$report) {
            return redirect()->route('home');
        }

        return Inertia::render('Public/SubmitEmail', [
            'report' => [
                'id' => $report->id,
                'url' => $report->url,
                'report_type' => $report->report_type,
            ],
        ]);
    }

    public function submitEmail(Request $request, StripeCheckoutService $stripe)
    {
        $validated = $request->validate(
            [
                'email' => ['required', 'email'],
                'report_id' => ['required', 'exists:reports,id'],
            ],
            [
                'email.required' => __('wizard_email_required'),
                'email.email' => __('wizard_email_invalid'),
            ],
        );

        $report = Report::findOrFail($validated['report_id']);

        if ($report->status !== 'pending') {
            if ($report->page_token) {
                return redirect()->route('report.status', ['pageToken' => $report->page_token]);
            }

            return redirect()->route('home')->withErrors(['error' => 'This report is no longer pending.']);
        }

        $pageToken = hash('sha256', $validated['email'] . $report->url . $report->report_type);

        // If a report with this page_token already exists, redirect to it
        $existingByToken = Report::where('page_token', $pageToken)->first();
        if ($existingByToken && $existingByToken->id !== $report->id) {
            $report->delete();
            session()->forget('report_id');

            Log::channel('report')->info('Duplicate submission — redirecting to existing report', [
                'existing_report_id' => $existingByToken->id,
            ]);

            return redirect()->route('report.status', ['pageToken' => $pageToken]);
        }

        $report->update([
            'email' => $validated['email'],
            'page_token' => $pageToken,
            'error_message' => null,
        ]);

        try {
            $checkoutUrl = $stripe->createCheckoutSession($report);
        } catch (\Throwable $e) {
            Log::channel('stripe')->error('Unable to start Stripe checkout for report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'email' => __('payment_unavailable'),
            ]);
        }

        session()->forget('report_id');

        return Inertia::location($checkoutUrl);
    }

    public function retryCheckout(string $pageToken, StripeCheckoutService $stripe)
    {
        $report = Report::where('page_token', $pageToken)->firstOrFail();

        if (!in_array($report->status, ['awaiting_payment', 'payment_cancelled', 'payment_failed'], true)) {
            return redirect()->route('report.status', ['pageToken' => $pageToken])
                ->with('error', __('payment_retry_unavailable'));
        }

        if (!$report->email) {
            return redirect()->route('report.status', ['pageToken' => $pageToken])
                ->with('error', __('payment_unavailable'));
        }

        try {
            $checkoutUrl = $stripe->createCheckoutSession($report);
        } catch (\Throwable $e) {
            Log::channel('stripe')->error('Unable to retry Stripe checkout for report', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('report.status', ['pageToken' => $pageToken])
                ->with('error', __('payment_unavailable'));
        }

        return Inertia::location($checkoutUrl);
    }

    public function paymentSuccess(Request $request, string $pageToken, StripeCheckoutService $stripe)
    {
        $report = Report::where('page_token', $pageToken)->firstOrFail();
        $purchaseId = $request->integer('purchase');

        try {
            $stripe->markCheckoutSuccessReturn(
                $report,
                $purchaseId > 0 ? $purchaseId : null,
                $request->query('session_id'),
            );
        } catch (\Throwable $e) {
            Log::channel('stripe')->error('Unable to sync Stripe success redirect', [
                'report_id' => $report->id,
                'purchase_id' => $purchaseId > 0 ? $purchaseId : null,
                'session_id' => $request->query('session_id'),
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('report.status', ['pageToken' => $pageToken])
                ->with('error', __('payment_status_sync_error'));
        }

        return redirect()->route('report.status', ['pageToken' => $pageToken])
            ->with('success', __('payment_return_success'));
    }

    public function paymentCancel(Request $request, string $pageToken, StripeCheckoutService $stripe)
    {
        $report = Report::where('page_token', $pageToken)->firstOrFail();
        $purchaseId = $request->integer('purchase');

        try {
            $stripe->markCheckoutCanceled($report, $purchaseId > 0 ? $purchaseId : null);
        } catch (\Throwable $e) {
            Log::channel('stripe')->error('Unable to sync Stripe cancel redirect', [
                'report_id' => $report->id,
                'purchase_id' => $purchaseId > 0 ? $purchaseId : null,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('report.status', ['pageToken' => $pageToken])
                ->with('error', __('payment_status_sync_error'));
        }

        return redirect()->route('report.status', ['pageToken' => $pageToken])
            ->with('error', __('payment_return_cancelled'));
    }

    public function status(string $pageToken)
    {
        $report = Report::where('page_token', $pageToken)->firstOrFail();

        return Inertia::render('Public/ReportStatus', [
            'report' => $this->reportStatusPayload($report),
            'pageToken' => $pageToken,
        ]);
    }

    public function statusJson(string $pageToken)
    {
        $report = Report::where('page_token', $pageToken)->first();

        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }

        return response()->json($this->reportStatusPayload($report));
    }

    private function reportStatusPayload(Report $report): array
    {
        return [
            'url' => $report->url,
            'report_type' => $report->report_type,
            'status' => $report->status,
            'report_url' => null,
            'error_message' => $report->error_message,
        ];
    }

    private function publicWizardMaintenanceEnabled(): bool
    {
        return (bool) config('app.public_wizard_maintenance', false);
    }

    private function renderLegalDocument(string $documentKey)
    {
        $locale = app()->getLocale();
        $documents = [
            'privacy-policy' => [
                'title' => [
                    'en' => 'Privacy Policy',
                    'ro' => 'Politica de confidențialitate',
                ],
                'description' => [
                    'en' => 'Learn how Get Your Consultant collects, uses, stores, and protects personal data across the website and report generation flow.',
                    'ro' => 'Află cum Get Your Consultant colectează, utilizează, stochează și protejează datele personale pe website și în fluxul de generare a rapoartelor.',
                ],
                'file' => [
                    'en' => 'GYC_Privacy-Policy.md',
                    'ro' => 'GYC_Politica-de-Confidentialitate.md',
                ],
                'path' => '/privacy-policy',
            ],
            'terms-and-conditions' => [
                'title' => [
                    'en' => 'Terms and Conditions',
                    'ro' => 'Termeni și condiții',
                ],
                'description' => [
                    'en' => 'Read the contractual terms for using Get Your Consultant, ordering reports, payments, delivery, refunds, and digital-content rights.',
                    'ro' => 'Consultă termenii contractuali pentru utilizarea Get Your Consultant, comandarea rapoartelor, plăți, livrare, rambursări și drepturile asupra conținutului digital.',
                ],
                'file' => [
                    'en' => 'GYC_Terms-and-Conditions.md',
                    'ro' => 'GYC_Termeni-si-Conditii.md',
                ],
                'path' => '/terms-and-conditions',
            ],
            'cookie-policy' => [
                'title' => [
                    'en' => 'Cookie Policy',
                    'ro' => 'Politica de cookie-uri',
                ],
                'description' => [
                    'en' => 'See what cookies Get Your Consultant uses, why they are used, how consent works, and how you can manage your preferences.',
                    'ro' => 'Vezi ce cookie-uri folosește Get Your Consultant, de ce sunt folosite, cum funcționează consimțământul și cum îți poți gestiona preferințele.',
                ],
                'file' => [
                    'en' => 'GYC_Cookie-Policy.md',
                    'ro' => 'GYC_Politica-de-Cookies.md',
                ],
                'path' => '/cookie-policy',
            ],
        ];

        abort_unless(isset($documents[$documentKey]), 404);

        $document = $documents[$documentKey];
        $filePath = storage_path('app/public/'.$document['file'][$locale]);

        abort_unless(file_exists($filePath), 404);

        $markdown = file_get_contents($filePath);
        $canonicalPath = $document['path'];
        $alternates = collect(LocalizedUrl::publicLocales())
            ->mapWithKeys(fn (string $publicLocale) => [
                $publicLocale === 'ro' ? 'ro-RO' : 'en-US' => LocalizedUrl::publicUrlForLocale($publicLocale, $canonicalPath),
            ])
            ->all();

        $converter = new GithubFlavoredMarkdownConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return response()->view('public.legal-document', [
            'title' => $document['title'][$locale],
            'description' => $document['description'][$locale],
            'contentHtml' => (string) $converter->convert($markdown),
            'canonical' => LocalizedUrl::publicUrlForLocale($locale, $canonicalPath),
            'alternates' => $alternates,
            'xDefault' => LocalizedUrl::publicUrlForLocale(LocalizedUrl::publicXDefaultLocale(), $canonicalPath),
        ]);
    }

    private function resolveUrlValidationMessage(?string $reasonCode): string
    {
        return match ($reasonCode) {
            'not_property' => __('wizard_url_not_property'),
            'not_buying_property' => __('wizard_url_not_buying_property'),
            'not_renting_property' => __('wizard_url_not_renting_property'),
            'source_blocked', 'accessible_property', null => __('wizard_url_access_failed'),
            default => __('wizard_url_access_failed'),
        };
    }
}
