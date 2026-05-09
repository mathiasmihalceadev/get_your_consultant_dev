<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'rental_living_prompt' => ['required', 'string'],
            'rental_living_prompt_ro' => ['required', 'string'],
            'rental_business_prompt' => ['required', 'string'],
            'rental_business_prompt_ro' => ['required', 'string'],
            'buying_living_prompt' => ['required', 'string'],
            'buying_living_prompt_ro' => ['required', 'string'],
            'buying_business_prompt' => ['required', 'string'],
            'buying_business_prompt_ro' => ['required', 'string'],
            'auto_send' => ['boolean'],
        ]);

        foreach ($validated as $key => $value) {
            Settings::set($key, $value);
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    public function testPdf(Request $request)
    {
        $type = $request->query('type', 'rental');

        $config = match ($type) {
            'buying' => [
                'json' => 'buying_ro.json',
                'template' => 'reports.template-buying',
                'label' => 'buying',
            ],
            default => [
                'json' => 'rental_ro_v2.json',
                'template' => 'reports.template-rental',
                'label' => 'rental',
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

        $footerHtml = '<div style="width:100%;text-align:center;font-family:Inter,sans-serif;padding:0 40px;line-height:1.4;">'
            . '<div style="font-size:8px;color:#9CA3AF;font-style:italic;">Raport informativ generat prin analiza datelor publice disponibile È™i utilizarea unor modele statistice proprietare dezvoltate de Get Your Consultant.</div>'
            . '<div style="font-size:8px;color:#9CA3AF;font-style:italic;">Datele prezentate au caracter informativ È™i pot necesita verificare independentÄƒ.</div>'
            . '<div style="font-size:7px;color:#B0B0B0;margin-top:2px;">Â© 2026 Get Your Consultant. Toate drepturile rezervate.</div>'
            . '</div>';

        $pdf = Pdf::view($template, ['data' => $data])
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
}

