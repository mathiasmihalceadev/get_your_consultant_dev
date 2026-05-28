<?php

namespace Tests\Unit;

use App\Services\ExchangeRateService;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class ExchangeRateServiceTest extends TestCase
{
    public function test_it_fetches_the_eur_to_ron_rate_from_the_configured_provider(): void
    {
        config()->set(
            'services.exchange_rates.eur_ron_url',
            'https://api.frankfurter.app/latest?from=EUR&to=RON',
        );

        Http::fake([
            'https://api.frankfurter.app/latest?from=EUR&to=RON' => Http::response([
                'amount' => 1,
                'base' => 'EUR',
                'date' => '2026-05-23',
                'rates' => [
                    'RON' => 5.0476,
                ],
            ]),
        ]);

        $result = app(ExchangeRateService::class)->fetchEurToRonRate();

        $this->assertSame('5.047600', $result['rate']);
        $this->assertSame('2026-05-23', $result['date']);
        $this->assertSame('Frankfurter', $result['provider']);
    }

    public function test_it_throws_when_the_provider_response_does_not_contain_a_valid_rate(): void
    {
        config()->set(
            'services.exchange_rates.eur_ron_url',
            'https://api.frankfurter.app/latest?from=EUR&to=RON',
        );

        Http::fake([
            'https://api.frankfurter.app/latest?from=EUR&to=RON' => Http::response([
                'amount' => 1,
                'base' => 'EUR',
                'date' => '2026-05-23',
                'rates' => [],
            ]),
        ]);

        $this->expectException(RuntimeException::class);

        app(ExchangeRateService::class)->fetchEurToRonRate();
    }
}