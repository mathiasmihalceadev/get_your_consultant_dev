<?php

namespace Tests\Feature;

use App\Services\ReportPricingService;
use Mockery\MockInterface;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('locales.supported', ['en', 'ro']);
        config()->set('locales.public', ['ro']);
        config()->set('locales.default', 'en');
        config()->set('locales.domain_urls', [
            'en' => 'http://myapp-com.test:8000',
            'ro' => 'http://myapp-ro.test:8000',
        ]);
        config()->set('locales.host_locale_map', [
            'myapp-com.test' => 'en',
            'myapp-ro.test' => 'ro',
        ]);
        config()->set('locales.x_default_locale', 'ro');
        config()->set('seo.indexing', true);
        config()->set('app.url', 'http://myapp-ro.test:8000');
    }

    public function test_home_page_renders_server_side_landing_markup(): void
    {
        $this->mock(ReportPricingService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('catalogForRequest')
                ->once()
                ->andReturn([
                    'buying_living' => [
                        'base_currency' => 'RON',
                        'base_amount_minor' => 2799,
                    ],
                    'rental_living' => [
                        'base_currency' => 'RON',
                        'base_amount_minor' => 1799,
                    ],
                ]);
        });

        $response = $this->withServerVariables([
            'HTTP_HOST' => 'myapp-ro.test',
        ])->get('/');

        $response
            ->assertOk()
            ->assertSee('Verifică o proprietate înainte să cumperi/închiriezi.', false)
            ->assertSee('id="hero"', false)
            ->assertDontSee('data-page=', false);
    }
}