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
    public function index()
    {
        return Inertia::render('Public/Index');
    }

    public function showUrlForm(Request $request)
    {
        $type = $request->query('type');

        if (!in_array($type, ['purchase', 'rental', 'commercial'])) {
            return redirect('/');
        }

        return Inertia::render('Public/SubmitUrl', [
            'reportType' => $type,
        ]);
    }

    public function validateUrl(Request $request, OpenAIService $openAI)
    {
        $validated = $request->validate([
            'url' => ['required', 'url'],
            'report_type' => ['required', 'in:purchase,rental,commercial'],
        ]);

        $report = Report::create([
            'url' => $validated['url'],
            'report_type' => $validated['report_type'],
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
                'url' => 'This URL could not be accessed or does not appear to be a property listing.',
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
                'url' => 'This URL could not be accessed or does not appear to be a property listing.',
            ]);
        }

        session(['report_id' => $report->id]);

        Log::channel('report')->info('URL validation passed', [
            'report_id' => $report->id,
        ]);

        return redirect('/submit-email');
    }

    public function showEmailForm()
    {
        $reportId = session('report_id');

        if (!$reportId) {
            return redirect('/');
        }

        $report = Report::find($reportId);

        if (!$report) {
            return redirect('/');
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
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'report_id' => ['required', 'exists:reports,id'],
        ]);

        $report = Report::findOrFail($validated['report_id']);

        if ($report->status !== 'pending') {
            return redirect('/')->withErrors(['error' => 'This report is no longer pending.']);
        }

        $pageToken = hash('sha256', $validated['email'] . $report->url . $report->report_type);

        $report->update([
            'email' => $validated['email'],
            'page_token' => $pageToken,
        ]);

        // Duplicate URL + type check
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

            $settings = Settings::first();

            if ($settings && $settings->auto_send) {
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

        return redirect("/report/{$pageToken}");
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
}
