<?php

namespace App\Services;

use App\Mail\ReportFeedbackMail;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class ReportFeedbackService
{
    public function send(Report $report, bool $force = false): bool
    {
        if (!$this->canSend($report)) {
            throw new RuntimeException('Feedback email cannot be sent for this report.');
        }

        if (!$force && $report->feedback_sent_at !== null) {
            return false;
        }

        Mail::to($report->email)->send(new ReportFeedbackMail($report));

        $report->forceFill([
            'feedback_sent_at' => now(),
        ])->save();

        Log::channel('report')->info('Report feedback email sent', [
            'report_id' => $report->id,
            'email' => $report->email,
            'forced' => $force,
        ]);

        return true;
    }

    public function canSend(Report $report): bool
    {
        return !$report->is_test
            && $report->email !== null
            && $report->page_token !== null
            && $report->processed_at !== null
            && in_array($report->status, ['to_be_sent', 'sent'], true);
    }
}
