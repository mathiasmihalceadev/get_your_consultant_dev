<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ExchangeRateService
{
    public function fetchEurToRonRate(): array
    {
        $url = (string) config(
            'services.exchange_rates.eur_ron_url',
            'https://api.frankfurter.app/latest?from=EUR&to=RON',
        );

        $response = Http::acceptJson()
            ->timeout((int) config('services.exchange_rates.timeout', 10))
            ->get($url);

        if ($response->failed()) {
            throw new RuntimeException('Exchange rate provider request failed.');
        }

        $payload = $response->json();
        $rate = data_get($payload, 'rates.RON');

        if (!is_numeric($rate) || (float) $rate <= 0) {
            throw new RuntimeException('Exchange rate provider returned an invalid EUR to RON rate.');
        }

        return [
            'base' => strtoupper((string) ($payload['base'] ?? 'EUR')),
            'target' => 'RON',
            'rate' => number_format((float) $rate, 6, '.', ''),
            'date' => (string) ($payload['date'] ?? now()->toDateString()),
            'provider' => 'Frankfurter',
        ];
    }
}