<?php

namespace App\Services;

use App\Mail\ReportAutoSendFailedMail;
use App\Mail\ReportMail;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AutomaticReportDeliveryService
{
    private const MONITOR_EMAIL = 'mathias.mihalcea@gmail.com';

    public function sendOrFallback(Report $report): bool
    {
        try {
            $pendingMail = Mail::to($report->email);

            if (strcasecmp((string) $report->email, self::MONITOR_EMAIL) !== 0) {
                $pendingMail->bcc(self::MONITOR_EMAIL);
            }

            $pendingMail->send(new ReportMail($report));

            Log::channel('report')->info('Report automatically sent to customer', [
                'report_id' => $report->id,
                'email' => $report->email,
                'monitor_email' => self::MONITOR_EMAIL,
            ]);

            return true;
        } catch (Throwable $exception) {
            $this->handleFailure($report, $exception);

            return false;
        }
    }

    private function handleFailure(Report $report, Throwable $exception): void
    {
        $report->update([
            'status' => 'to_be_sent',
            'error_message' => 'Automatic report email failed: ' . $exception->getMessage(),
            'processed_at' => $report->processed_at ?: now(),
        ]);

        Log::channel('report')->error('Automatic report email failed', [
            'report_id' => $report->id,
            'email' => $report->email,
            'monitor_email' => self::MONITOR_EMAIL,
            'error' => $exception->getMessage(),
        ]);

        try {
            Mail::to(self::MONITOR_EMAIL)->send(
                new ReportAutoSendFailedMail($report, $exception->getMessage()),
            );
        } catch (Throwable $notificationException) {
            Log::channel('report')->error('Automatic report failure notification failed', [
                'report_id' => $report->id,
                'monitor_email' => self::MONITOR_EMAIL,
                'error' => $notificationException->getMessage(),
            ]);
        }
    }
}
