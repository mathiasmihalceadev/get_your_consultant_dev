<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportFeedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportFeedbackController extends Controller
{
    public function show(string $pageToken): Response
    {
        $report = Report::with('feedback')
            ->where('page_token', $pageToken)
            ->firstOrFail();

        return Inertia::render('Public/Feedback', [
            'pageToken' => $pageToken,
            'report' => [
                'id' => $report->id,
                'report_type' => $report->report_type,
                'locale' => $this->locale($report),
                'url' => $report->url,
            ],
            'submitted' => $report->feedback !== null,
        ]);
    }

    public function store(Request $request, string $pageToken): RedirectResponse
    {
        $report = Report::where('page_token', $pageToken)->firstOrFail();

        if ($report->feedback()->exists()) {
            return redirect()
                ->route('report.feedback.show', ['pageToken' => $pageToken])
                ->with('success', $this->successMessage($report, alreadySubmitted: true));
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,10'],
            'most_useful_info' => ['required', 'string', 'max:2000'],
            'wanted_extra' => ['nullable', 'string', 'max:2000'],
            'would_recommend' => ['required', 'boolean'],
            'trust_improvement' => ['nullable', 'string', 'max:2000'],
        ]);

        ReportFeedback::create([
            'report_id' => $report->id,
            'rating' => $validated['rating'],
            'most_useful_info' => $validated['most_useful_info'],
            'wanted_extra' => $validated['wanted_extra'] ?? null,
            'would_recommend' => $validated['would_recommend'],
            'trust_improvement' => $validated['trust_improvement'] ?? null,
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('report.feedback.show', ['pageToken' => $pageToken])
            ->with('success', $this->successMessage($report));
    }

    private function locale(Report $report): string
    {
        return strtolower((string) ($report->locale ?? 'ro')) === 'en' ? 'en' : 'ro';
    }

    private function successMessage(Report $report, bool $alreadySubmitted = false): string
    {
        if ($this->locale($report) === 'en') {
            return $alreadySubmitted
                ? 'Your feedback has already been recorded. Thank you!'
                : 'Thank you for your feedback!';
        }

        return $alreadySubmitted
            ? "Feedback-ul t\u{0103}u a fost deja \u{00EE}nregistrat. Mul\u{021B}umim!"
            : "Mul\u{021B}umim pentru feedback!";
    }
}
