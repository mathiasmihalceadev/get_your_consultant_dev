<?php

namespace Tests\Unit;

use App\Mail\ReportMail;
use App\Models\Report;
use App\Services\AutomaticReportDeliveryService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AutomaticReportDeliveryServiceTest extends TestCase
{
    public function test_it_copies_automatic_report_emails_to_configured_settings_recipients(): void
    {
        Mail::fake();

        $report = (new Report())->forceFill([
            'id' => 42,
            'report_type' => 'buying_living',
            'locale' => 'ro',
            'email' => 'buyer@example.com',
            'url' => 'https://example.com/property/42',
            'status' => 'pending',
            'report_url' => 'reports/gyc_02041.pdf',
        ]);

        $sent = app(AutomaticReportDeliveryService::class)->sendOrFallback(
            $report,
            ['ops@example.com', 'team@example.com', 'buyer@example.com'],
        );

        $this->assertTrue($sent);

        Mail::assertSent(ReportMail::class, 1);
        Mail::assertSent(ReportMail::class, function (ReportMail $mail): bool {
            return $mail->hasTo('buyer@example.com')
                && $mail->hasBcc('ops@example.com')
                && $mail->hasBcc('team@example.com')
                && !$mail->hasBcc('buyer@example.com')
                && !$mail->hasBcc('mathias.mihalcea@gmail.com');
        });
    }
}
