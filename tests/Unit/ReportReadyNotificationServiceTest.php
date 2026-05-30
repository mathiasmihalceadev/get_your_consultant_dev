<?php

namespace Tests\Unit;

use App\Mail\ReportReadyNotificationMail;
use App\Models\Report;
use App\Services\ReportReadyNotificationService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReportReadyNotificationServiceTest extends TestCase
{
    public function test_it_sends_internal_notifications_to_each_configured_recipient(): void
    {
        Mail::fake();

        $report = (new Report())->forceFill([
            'id' => 42,
            'report_type' => 'buying_living',
            'locale' => 'ro',
            'email' => 'buyer@example.com',
            'url' => 'https://example.com/property/42',
            'status' => 'to_be_sent',
            'processed_at' => now(),
        ]);

        $service = app(ReportReadyNotificationService::class);
        $service->send($report, ['ops@example.com', 'team@example.com']);

        Mail::assertSent(ReportReadyNotificationMail::class, 2);
        Mail::assertSent(ReportReadyNotificationMail::class, function (ReportReadyNotificationMail $mail): bool {
            return $mail->report->id === 42 && $mail->hasTo('ops@example.com');
        });
        Mail::assertSent(ReportReadyNotificationMail::class, function (ReportReadyNotificationMail $mail): bool {
            return $mail->report->id === 42 && $mail->hasTo('team@example.com');
        });
    }
}