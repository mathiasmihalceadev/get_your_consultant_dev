<?php

namespace App\Services;

use App\Support\ReportPdfFooter;
use DateTimeInterface;
use Illuminate\Support\Facades\Http;

class RemotePdfRenderer
{
    public function saveView(string $view, array $viewData, string $path, string $filename, ?DateTimeInterface $generatedAt = null): void
    {
        $html = view($view, $viewData)->render();
        $footerHtml = ReportPdfFooter::render($generatedAt, $this->resolveLocale($viewData));
        $pdfContent = $this->renderHtml($html, $footerHtml, $filename);

        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_put_contents($path, $pdfContent) === false) {
            throw new \RuntimeException("Unable to write rendered PDF to [{$path}].");
        }
    }

    public function renderHtml(string $html, string $footerHtml, string $filename): string
    {
        $serviceUrl = $this->serviceUrl();
        $sharedSecret = $this->sharedSecret();

        $response = Http::timeout((int) config('pdf_renderer.timeout', 180))
            ->accept('application/pdf')
            ->withHeaders([
                'X-Render-Token' => $sharedSecret,
            ])
            ->post($serviceUrl, [
                'html' => $html,
                'footer_html' => $footerHtml,
                'filename' => $filename,
            ]);

        if (! $response->successful()) {
            $body = trim($response->body());

            throw new \RuntimeException(
                sprintf(
                    'Remote PDF renderer failed with HTTP %d%s',
                    $response->status(),
                    $body !== '' ? ': '.$body : '.'
                )
            );
        }

        return $response->body();
    }

    private function serviceUrl(): string
    {
        $serviceUrl = trim((string) config('pdf_renderer.service_url'));

        if ($serviceUrl === '' || str_contains($serviceUrl, 'replace-this-with-your-vps-domain')) {
            throw new \RuntimeException('The PDF renderer service URL is not configured in config/pdf_renderer.php.');
        }

        return $serviceUrl;
    }

    private function sharedSecret(): string
    {
        $sharedSecret = trim((string) config('pdf_renderer.shared_secret'));

        if ($sharedSecret === '' || str_contains($sharedSecret, 'replace-this-with-a-long-random-shared-secret')) {
            throw new \RuntimeException('The PDF renderer shared secret is not configured in config/pdf_renderer.php.');
        }

        return $sharedSecret;
    }

    private function resolveLocale(array $viewData): string
    {
        $locale = $viewData['locale']
            ?? ($viewData['report']->locale ?? null)
            ?? ($viewData['data']['report_meta']['locale'] ?? null)
            ?? ($viewData['data']['locale'] ?? null)
            ?? 'ro';

        return strtolower((string) $locale) === 'en' ? 'en' : 'ro';
    }
}