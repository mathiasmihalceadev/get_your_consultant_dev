<?php

namespace App\Services;

use App\Mail\ReportReadyNotificationMail;
use App\Models\Report;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReportReadyNotificationService
{
    /**
     * @param  array<int, string>|null  $recipients
     */
    public function send(Report $report, ?array $recipients = null): void
    {
        if ($report->status !== 'to_be_sent' || $report->is_test) {
            return;
        }

        $resolvedRecipients = $recipients ?? Settings::reportReadyNotificationRecipients();

        if ($resolvedRecipients === []) {
            return;
        }

        foreach ($resolvedRecipients as $recipient) {
            Mail::to($recipient)->send(new ReportReadyNotificationMail($report));
        }

        Log::channel('report')->info('Report-ready notifications sent to internal recipients', [
            'report_id' => $report->id,
            'recipient_count' => count($resolvedRecipients),
        ]);
    }
}