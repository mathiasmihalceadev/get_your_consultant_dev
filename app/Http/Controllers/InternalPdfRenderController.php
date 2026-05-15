<?php

namespace App\Http\Controllers;

use App\Support\BrowsershotConfigurator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;

class InternalPdfRenderController extends Controller
{
    public function __invoke(Request $request)
    {
        $expectedToken = trim((string) config('pdf_renderer.shared_secret'));

        if ($expectedToken === '' || str_contains($expectedToken, 'replace-this-with-a-long-random-shared-secret')) {
            Log::error('Internal PDF render endpoint is not configured with a shared secret.');

            return response()->json([
                'message' => 'PDF render endpoint is not configured.',
            ], 500);
        }

        $providedToken = (string) $request->header('X-Render-Token', '');

        if (! hash_equals($expectedToken, $providedToken)) {
            return response()->json([
                'message' => 'Forbidden.',
            ], 403);
        }

        $validated = $request->validate([
            'html' => ['required', 'string'],
            'footer_html' => ['nullable', 'string'],
            'filename' => ['nullable', 'string', 'max:255'],
        ]);

        $filename = trim((string) ($validated['filename'] ?? 'rendered-report.pdf'));
        $filename = Str::endsWith(strtolower($filename), '.pdf') ? $filename : $filename.'.pdf';

        try {
            $pdf = Pdf::html($validated['html'])
                ->format('a4')
                ->name($filename)
                ->withBrowsershot(function ($browsershot) use ($validated) {
                    BrowsershotConfigurator::apply($browsershot)
                        ->waitUntilNetworkIdle();

                    $footerHtml = trim((string) ($validated['footer_html'] ?? ''));

                    if ($footerHtml !== '') {
                        $browsershot->showBrowserHeaderAndFooter()
                            ->headerHtml('<div></div>')
                            ->footerHtml($footerHtml);
                    }
                });

            return response($pdf->generatePdfContent(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        } catch (\Throwable $e) {
            Log::error('Internal PDF render failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'PDF render failed.',
            ], 500);
        }
    }
}