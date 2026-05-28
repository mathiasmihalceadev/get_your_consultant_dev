<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Settings;
use App\Services\ExchangeRateService;
use App\Services\RemotePdfRenderer;
use App\Support\ReportDataNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AdminSettingsController extends Controller
{
    public function show()
    {
        return Inertia::render('Admin/Settings', [
            'settings' => Settings::getAllSettings(),
            'billingTests' => Report::query()
                ->where('is_test', true)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get(),
            'billingTestCompletedCount' => Report::where('status', 'test_completed')->count(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'rental_living_ro' => ['required', 'string'],
            'rental_living_eng' => ['required', 'string'],
            'buying_living_ro' => ['required', 'string'],
            'buying_living_eng' => ['required', 'string'],
            'auto_send' => ['boolean'],
            'pricing_rental_living_eur' => ['required', 'numeric', 'gt:0'],
            'pricing_buying_living_eur' => ['required', 'numeric', 'gt:0'],
            'pricing_exchange_rate_eur_ron' => ['required', 'numeric', 'gt:0'],
            'stripe_product_rental_living' => ['nullable', 'string', 'max:255'],
            'stripe_product_buying_living' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['pricing_rental_living_eur'] = number_format(
            (float) $validated['pricing_rental_living_eur'],
            2,
            '.',
            '',
        );
        $validated['pricing_buying_living_eur'] = number_format(
            (float) $validated['pricing_buying_living_eur'],
            2,
            '.',
            '',
        );
        $validated['pricing_exchange_rate_eur_ron'] = number_format(
            (float) $validated['pricing_exchange_rate_eur_ron'],
            6,
            '.',
            '',
        );
        $validated['stripe_product_rental_living'] = trim((string) ($validated['stripe_product_rental_living'] ?? ''));
        $validated['stripe_product_buying_living'] = trim((string) ($validated['stripe_product_buying_living'] ?? ''));

        foreach ($validated as $key => $value) {
            Settings::set($key, $value);
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    public function exchangeRate(ExchangeRateService $exchangeRates)
    {
        try {
            $rate = $exchangeRates->fetchEurToRonRate();
        } catch (\Throwable $e) {
            Log::warning('Unable to fetch EUR to RON exchange rate', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Nu am putut prelua cursul EUR → RON acum. Încearcă din nou în câteva momente.',
            ], 422);
        }

        return response()->json([
            'message' => 'Cursul a fost preluat cu succes.',
            'rate' => $rate['rate'],
            'date' => $rate['date'],
            'provider' => $rate['provider'],
        ]);
    }

    public function testPdf(Request $request, RemotePdfRenderer $pdfRenderer)
    {
        $type = $request->query('type', 'rental_living_ro');

        $config = match ($type) {
            'buying_living_ro' => [
                'json' => 'buying_ro.json',
                'template' => 'reports.template-buying',
                'label' => 'buying-ro',
                'locale' => 'ro',
            ],
            'buying_living_eng' => [
                'json' => 'buying_eng.json',
                'template' => 'reports.template-buying',
                'label' => 'buying-eng',
                'locale' => 'en',
            ],
            'rental_living_eng' => [
                'json' => 'rental_eng.json',
                'template' => 'reports.template-rental',
                'label' => 'rental-eng',
                'locale' => 'en',
            ],
            default => [
                'json' => 'rental_ro.json',
                'template' => 'reports.template-rental',
                'label' => 'rental-ro',
                'locale' => 'ro',
            ],
        };

        $jsonPath = storage_path('app/' . $config['json']);

        if (!file_exists($jsonPath)) {
            return back()->with('error', "Mock JSON file not found at storage/app/{$config['json']}");
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return back()->with('error', 'Invalid JSON in mock file: ' . json_last_error_msg());
        }

        $data = ReportDataNormalizer::normalize($data, $config['locale']);

        $template = $config['template'];
        $label = $config['label'];
        $filename = "test-report-{$label}-" . now()->format('Ymd-His') . '.pdf';
        $path = storage_path('app/public/reports/' . $filename);

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        try {
            $pdfRenderer->saveView($template, [
                'data' => $data,
                'locale' => $config['locale'],
                'trans' => $this->loadTranslations($config['locale']),
            ], $path, $filename, now());
        } catch (\Throwable $e) {
            Log::error('Admin test PDF generation failed', [
                'type' => $type,
                'template' => $template,
                'json' => $config['json'],
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Test PDF generation failed. Check the server log and Browsershot/Chromium configuration.');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function loadTranslations(string $locale): array
    {
        $path = lang_path("{$locale}.json");

        if (!file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?? [];
    }
}

