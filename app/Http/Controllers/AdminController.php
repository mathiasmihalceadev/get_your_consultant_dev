<?php

namespace App\Http\Controllers;

use App\Mail\ReportMail;
use App\Models\Report;
use App\Services\ReportFeedbackService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Report::query()->with('feedback');

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('report_type')) {
            $query->where('report_type', $request->query('report_type'));
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        $counts = [
            'total' => Report::count(),
            'pending' => Report::where('status', 'pending')->count(),
            'awaiting_payment' => Report::where('status', 'awaiting_payment')->count(),
            'payment_processing' => Report::where('status', 'payment_processing')->count(),
            'payment_cancelled' => Report::where('status', 'payment_cancelled')->count(),
            'payment_failed' => Report::where('status', 'payment_failed')->count(),
            'test_completed' => Report::where('status', 'test_completed')->count(),
            'to_be_sent' => Report::where('status', 'to_be_sent')->count(),
            'sent' => Report::where('status', 'sent')->count(),
            'error' => Report::where('status', 'error')->count(),
            'not_accessible' => Report::where('status', 'not_accessible')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'reports' => $reports,
            'billingTests' => Report::query()
                ->where('is_test', true)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
            'counts' => $counts,
            'filters' => [
                'status' => $request->query('status', ''),
                'report_type' => $request->query('report_type', ''),
            ],
        ]);
    }

    public function show(int $id)
    {
        $report = Report::with(['latestPurchase.smartBillInvoice', 'feedback'])->findOrFail($id);

        return Inertia::render('Admin/ReportDetail', [
            'report' => $report,
        ]);
    }

    public function feedbacks()
    {
        $reports = Report::query()
            ->with('feedback')
            ->whereNotNull('feedback_sent_at')
            ->orderByDesc('feedback_sent_at')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/Feedbacks', [
            'reports' => $reports,
            'counts' => [
                'sent' => Report::whereNotNull('feedback_sent_at')->count(),
                'received' => Report::whereHas('feedback')->count(),
                'pending' => Report::whereNotNull('feedback_sent_at')
                    ->whereDoesntHave('feedback')
                    ->count(),
            ],
        ]);
    }

    public function createBillingTestCheckout(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'locale' => ['required', 'in:ro,en'],
            'report_type' => ['required', 'in:rental_living,buying_living'],
            'send_test_email' => ['nullable', 'boolean'],
        ]);

        $uuid = (string) Str::uuid();
        $baseUrl = rtrim((string) config('app.url', 'https://example.test'), '/');
        $report = Report::create([
            'report_type' => $validated['report_type'],
            'url' => $baseUrl . '/admin/billing-test/' . $uuid,
            'email' => $validated['email'],
            'locale' => $validated['locale'],
            'is_test' => true,
            'status' => 'pending',
            'page_token' => hash('sha256', 'billing-test|' . $uuid),
            'error_message' => null,
        ]);

        /** @var StripeCheckoutService $stripe */
        $stripe = app(StripeCheckoutService::class);

        try {
            $checkoutUrl = $stripe->createBillingTestCheckoutSession(
                $report,
                (bool) ($validated['send_test_email'] ?? false),
            );
        } catch (\Throwable $e) {
            Log::channel('stripe')->error('Unable to start Stripe billing test checkout', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            $report->update([
                'status' => 'error',
                'error_message' => 'Fluxul de test pentru Stripe + SmartBill nu a putut fi pornit.',
            ]);

            return back()->with('error', 'Fluxul de test pentru Stripe + SmartBill nu a putut fi pornit.');
        }

        return Inertia::location($checkoutUrl);
    }

    public function billingTestSuccess(Request $request, int $id, StripeCheckoutService $stripe)
    {
        $report = Report::where('is_test', true)->findOrFail($id);
        $purchaseId = $request->integer('purchase');

        try {
            $stripe->markCheckoutSuccessReturn(
                $report,
                $purchaseId > 0 ? $purchaseId : null,
                $request->query('session_id'),
            );
        } catch (\Throwable $e) {
            Log::channel('stripe')->error('Unable to sync Stripe billing test success redirect', [
                'report_id' => $report->id,
                'purchase_id' => $purchaseId > 0 ? $purchaseId : null,
                'session_id' => $request->query('session_id'),
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.reports.show', ['id' => $report->id])
            ->with('success', 'Checkout-ul de test a fost confirmat. SmartBill se sincronizeaza in fundal.');
    }

    public function billingTestCancel(Request $request, int $id, StripeCheckoutService $stripe)
    {
        $report = Report::where('is_test', true)->findOrFail($id);
        $purchaseId = $request->integer('purchase');

        $stripe->markCheckoutCanceled(
            $report,
            $purchaseId > 0 ? $purchaseId : null,
        );

        return redirect()
            ->route('admin.reports.show', ['id' => $report->id])
            ->with('success', 'Checkout-ul de test a fost anulat.');
    }

    public function send(int $id)
    {
        $report = Report::findOrFail($id);

        if ($report->is_test) {
            return back()->with('error', 'Fluxurile de test nu genereaza si nu trimit raportul final pe email.');
        }

        if (!$report->email) {
            return back()->with('error', 'Report cannot be sent because the email address is missing.');
        }

        $hasGeneratedReport = $report->hasStoredPdf();

        if (!$hasGeneratedReport) {
            return back()->with(
                'error',
                'Report email could not be sent because the generated report file is missing.',
            );
        }

        $isResend = $report->status === 'sent';

        try {
            Mail::to($report->email)->sendNow(new ReportMail($report));
        } catch (\Throwable $e) {
            Log::channel('report')->error('Admin report email failed to send', [
                'report_id' => $report->id,
                'email' => $report->email,
                'mailer' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'mail_scheme' => config('mail.mailers.smtp.scheme'),
                'from_address' => config('mail.from.address'),
                'queue_connection' => config('queue.default'),
                'pdf_path' => $report->storedPdfPath(),
                'pdf_exists' => $report->hasStoredPdf(),
                'error' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'Report email could not be sent. Check the report log for the exact mail error.',
            );
        }

        $report->update(['status' => 'sent']);

        Log::channel('report')->info('Report manually sent by admin', [
            'report_id' => $report->id,
            'action' => $isResend ? 'resent' : 'sent',
        ]);

        return back()->with(
            'success',
            $isResend
                ? "Report has been resent to {$report->email}."
                : "Report has been sent to {$report->email}.",
        );
    }

    public function sendFeedback(int $id, ReportFeedbackService $feedback): \Illuminate\Http\RedirectResponse
    {
        $report = Report::findOrFail($id);

        try {
            $feedback->send($report, force: true);
        } catch (\Throwable $e) {
            Log::channel('report')->error('Admin feedback email failed to send', [
                'report_id' => $report->id,
                'email' => $report->email,
                'error' => $e->getMessage(),
            ]);

            return back()->with(
                'error',
                'Feedback email could not be sent. The report must be generated, non-test, and have an email address.',
            );
        }

        return back()->with('success', "Feedback email has been sent to {$report->email}.");
    }

    public function pdf(int $id): BinaryFileResponse
    {
        $report = Report::findOrFail($id);
        $path = $report->storedPdfPath();

        abort_unless($path && is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $report->resolvedPdfStorageFilename() . '"',
        ]);
    }
}
