<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Support\ReportPdfFooter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\LaravelPdf\Facades\Pdf;

class AdminSettingsController extends Controller
{
    public function show()
    {
        return Inertia::render('Admin/Settings', [
            'settings' => Settings::getAllSettings(),
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
        ]);

        foreach ($validated as $key => $value) {
            Settings::set($key, $value);
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    public function testPdf(Request $request)
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

        $template = $config['template'];
        $label = $config['label'];
        $filename = "test-report-{$label}-" . now()->format('Ymd-His') . '.pdf';
        $path = storage_path('app/public/reports/' . $filename);

        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $footerHtml = ReportPdfFooter::render(now());

        $pdf = Pdf::view($template, [
            'data' => $data,
            'locale' => $config['locale'],
            'trans' => $this->loadTranslations($config['locale']),
        ])
            ->format('a4')
            ->withBrowsershot(function ($browsershot) use ($footerHtml) {
                $browsershot->waitUntilNetworkIdle()
                    ->showBrowserHeaderAndFooter()
                    ->headerHtml('<div></div>')
                    ->footerHtml($footerHtml);
            });

        $pdf->save($path);

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

