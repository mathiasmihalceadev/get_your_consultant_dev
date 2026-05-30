<?php

namespace Tests\Unit;

use App\Models\Settings;
use Tests\TestCase;

class SettingsNotificationEmailParsingTest extends TestCase
{
    public function test_it_normalizes_and_deduplicates_notification_emails(): void
    {
        $value = " Ops@example.com,\nteam@example.com ; ops@example.com\n\n finance@example.com ";

        $this->assertSame(
            [
                'ops@example.com',
                'team@example.com',
                'finance@example.com',
            ],
            Settings::parseNotificationEmailList($value),
        );

        $this->assertSame(
            implode("\n", [
                'ops@example.com',
                'team@example.com',
                'finance@example.com',
            ]),
            Settings::normalizeNotificationEmailList($value),
        );
    }
}