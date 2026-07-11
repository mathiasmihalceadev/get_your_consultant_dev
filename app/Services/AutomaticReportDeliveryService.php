<?php

namespace App\Services;

use App\Mail\ReportAutoSendFailedMail;
use App\Mail\ReportMail;
use App\Models\Report;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AutomaticReportDeliveryService
{
    /**
     * @param  array<int, string>|null  $notificationRecipients
     */
    public function sendOrFallback(Report $report, ?array $notificationRecipients = null): bool
    {
        $notificationRecipients ??= Settings::reportReadyNotificationRecipients();
        $bccRecipients = $this->excludeReportRecipient($notificationRecipients, $report);

        try {
            $pendingMail = Mail::to($report->email);

            if ($bccRecipients !== []) {
                $pendingMail->bcc($bccRecipients);
            }

            $pendingMail->send(new ReportMail($report));

            Log::channel('report')->info('Report automatically sent to customer', [
                'report_id' => $report->id,
                'email' => $report->email,
                'notification_recipient_count' => count($notificationRecipients),
                'bcc_recipient_count' => count($bccRecipients),
            ]);

            return true;
        } catch (Throwable $exception) {
            $this->handleFailure($report, $exception, $notificationRecipients);

            return false;
        }
    }

    /**
     * @param  array<int, string>  $notificationRecipients
     */
    private function handleFailure(Report $report, Throwable $exception, array $notificationRecipients): void
    {
        $report->update([
            'status' => 'to_be_sent',
            'error_message' => 'Automatic report email failed: ' . $exception->getMessage(),
            'processed_at' => $report->processed_at ?: now(),
        ]);

        Log::channel('report')->error('Automatic report email failed', [
            'report_id' => $report->id,
            'email' => $report->email,
            'notification_recipient_count' => count($notificationRecipients),
            'error' => $exception->getMessage(),
        ]);

        if ($notificationRecipients === []) {
            return;
        }

        try {
            foreach ($notificationRecipients as $recipient) {
                Mail::to($recipient)->send(
                    new ReportAutoSendFailedMail($report, $exception->getMessage()),
                );
            }
        } catch (Throwable $notificationException) {
            Log::channel('report')->error('Automatic report failure notification failed', [
                'report_id' => $report->id,
                'notification_recipient_count' => count($notificationRecipients),
                'error' => $notificationException->getMessage(),
            ]);
        }
    }

    /**
     * @param  array<int, string>  $recipients
     * @return array<int, string>
     */
    private function excludeReportRecipient(array $recipients, Report $report): array
    {
        $reportEmail = strtolower((string) $report->email);

        return array_values(array_filter(
            $recipients,
            fn (string $recipient): bool => strcasecmp($recipient, $reportEmail) !== 0,
        ));
    }
}
