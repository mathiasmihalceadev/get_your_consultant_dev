<?php

namespace App\Http\Controllers;

use App\Mail\ReportMail;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $query = Report::query();

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
            'to_be_sent' => Report::where('status', 'to_be_sent')->count(),
            'sent' => Report::where('status', 'sent')->count(),
            'error' => Report::where('status', 'error')->count(),
            'not_accessible' => Report::where('status', 'not_accessible')->count(),
        ];

        return Inertia::render('Admin/Dashboard', [
            'reports' => $reports,
            'counts' => $counts,
            'filters' => [
                'status' => $request->query('status', ''),
                'report_type' => $request->query('report_type', ''),
            ],
        ]);
    }

    public function show(int $id)
    {
        $report = Report::findOrFail($id);

        return Inertia::render('Admin/ReportDetail', [
            'report' => $report,
        ]);
    }

    public function send(int $id)
    {
        $report = Report::findOrFail($id);

        if ($report->status !== 'to_be_sent') {
            return back()->with('error', 'Report cannot be sent — status is not "to_be_sent".');
        }

        if (!$report->email) {
            return back()->with('error', 'Report cannot be sent because the email address is missing.');
        }

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
                'pdf_path' => $report->pdfStoragePath(),
                'pdf_exists' => file_exists($report->pdfStoragePath()),
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
        ]);

        return back()->with('success', "Report has been sent to {$report->email}.");
    }
}
