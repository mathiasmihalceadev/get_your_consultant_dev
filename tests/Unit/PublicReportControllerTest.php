<?php

namespace Tests\Unit;

use App\Http\Controllers\PublicReportController;
use App\Models\Report;
use ReflectionMethod;
use Tests\TestCase;

class PublicReportControllerTest extends TestCase
{
    public function test_public_status_payload_does_not_expose_the_report_path(): void
    {
        $controller = app(PublicReportController::class);
        $method = new ReflectionMethod($controller, 'reportStatusPayload');
        $report = new Report([
            'url' => 'https://example.com/property/123',
            'report_type' => 'buying_living',
            'status' => 'sent',
            'report_url' => 'reports/gyc_02000.pdf',
            'error_message' => null,
        ]);

        $method->setAccessible(true);

        $payload = $method->invoke($controller, $report);

        $this->assertArrayHasKey('report_url', $payload);
        $this->assertNull($payload['report_url']);
    }
}