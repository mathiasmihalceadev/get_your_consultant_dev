<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Settings;
use App\Jobs\GenerateReportJob;
use App\Mail\ReportMail;
use App\Services\OpenAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class PublicReportController extends Controller
{
    public function landing()
    {
        return Inertia::render('Public/Landing');
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
            $result = $openAI->validateUrl($validated['url']);
        } catch (\Exception $e) {
            $report->update([
                'status' => 'not_accessible',
                'error_message' => $e->getMessage(),
            ]);

            Log::channel('report')->error('URL validation exception', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'url' => __('wizard_url_access_failed'),
            ]);
        }

        if (!$result['success']) {
            $report->update([
                'status' => 'not_accessible',
                'error_message' => $result['message'],
            ]);

            Log::channel('report')->info('URL validation failed', [
                'report_id' => $report->id,
                'reason' => $result['message'],
            ]);

            return back()->withErrors([
                'url' => __('wizard_url_access_failed'),
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

    public function submitEmail(Request $request)
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
            return redirect()->route('home')->withErrors(['error' => 'This report is no longer pending.']);
        }

        $pageToken = hash('sha256', $validated['email'] . $report->url . $report->report_type);

        // If a report with this page_token already exists, redirect to it
        $existingByToken = Report::where('page_token', $pageToken)->first();
        if ($existingByToken) {
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
        ]);

        // Duplicate URL + type check (same URL+type but different email)
        $existing = Report::where('url', $report->url)
            ->where('report_type', $report->report_type)
            ->where('id', '!=', $report->id)
            ->whereNotNull('report_url')
            ->whereIn('status', ['sent', 'to_be_sent'])
            ->first();

        if ($existing) {
            Log::channel('report')->info('Duplicate detected — reusing existing PDF', [
                'report_id' => $report->id,
                'existing_report_id' => $existing->id,
            ]);

            $report->update(['report_url' => $existing->report_url]);

            if (Settings::get('auto_send')) {
                Mail::to($report->email)->send(new ReportMail($report));
                $report->update(['status' => 'sent', 'processed_at' => now()]);
            } else {
                $report->update(['status' => 'to_be_sent', 'processed_at' => now()]);
            }
        } else {
            $report->update(['status' => 'pending']);
            GenerateReportJob::dispatch($report->id);
            Log::channel('report')->info('GenerateReportJob dispatched', [
                'report_id' => $report->id,
            ]);
        }

        session()->forget('report_id');

        return redirect()->route('report.status', ['pageToken' => $pageToken]);
    }

    public function status(string $pageToken)
    {
        $report = Report::where('page_token', $pageToken)->firstOrFail();

        return Inertia::render('Public/ReportStatus', [
            'report' => [
                'url' => $report->url,
                'report_type' => $report->report_type,
                'status' => $report->status,
                'report_url' => $report->report_url,
                'error_message' => $report->error_message,
            ],
            'pageToken' => $pageToken,
        ]);
    }

    public function statusJson(string $pageToken)
    {
        $report = Report::where('page_token', $pageToken)->first();

        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }

        return response()->json([
            'status' => $report->status,
            'report_url' => $report->report_url,
        ]);
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
            ],
        ];

        abort_unless(isset($documents[$documentKey]), 404);

        $document = $documents[$documentKey];
        $filePath = storage_path('app/public/'.$document['file'][$locale]);

        abort_unless(file_exists($filePath), 404);

        $markdown = file_get_contents($filePath);

        return Inertia::render('Public/LegalDocument', [
            'title' => $document['title'][$locale],
            'description' => $document['description'][$locale],
            'markdown' => $markdown,
        ]);
    }
}
