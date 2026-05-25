<?php

declare(strict_types=1);

use App\Support\ReportDataNormalizer;
use App\Support\LocalizedUrl;
use App\Services\RemotePdfRenderer;
use Illuminate\Contracts\Console\Kernel;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\PageBoundaries;

require __DIR__ . '/vendor/autoload.php';

if (! function_exists('get_magic_quotes_runtime')) {
    function get_magic_quotes_runtime(): bool
    {
        return false;
    }
}

if (! function_exists('set_magic_quotes_runtime')) {
    function set_magic_quotes_runtime(int $newSetting): bool
    {
        return false;
    }
}

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

/** @var RemotePdfRenderer $renderer */
$renderer = $app->make(RemotePdfRenderer::class);

$variants = [
    'buying_en' => [
        'locale' => 'en',
        'mode' => 'existing',
        'source' => public_path('images/report-example-en.pdf'),
        'output' => public_path('images/report-example-en.pdf'),
        'report_type' => 'buying_living',
    ],
    'buying_ro' => [
        'locale' => 'ro',
        'mode' => 'existing',
        'source' => public_path('images/report-example-ro.pdf'),
        'output' => public_path('images/report-example-ro.pdf'),
        'report_type' => 'buying_living',
    ],
    'rental_en' => [
        'locale' => 'en',
        'mode' => 'rendered',
        'json' => storage_path('app/landing_example_rental_en.json'),
        'template' => 'reports.template-rental',
        'output' => public_path('images/report-example-rental-en.pdf'),
        'report_type' => 'rental_living',
    ],
    'rental_ro' => [
        'locale' => 'ro',
        'mode' => 'rendered',
        'json' => storage_path('app/landing_example_rental_ro.json'),
        'template' => 'reports.template-rental',
        'output' => public_path('images/report-example-rental-ro.pdf'),
        'report_type' => 'rental_living',
    ],
];

function loadTranslations(string $locale): array
{
    $path = lang_path(($locale === 'en' ? 'en' : 'ro') . '.json');

    if (! is_file($path)) {
        return [];
    }

    $translations = json_decode((string) file_get_contents($path), true);

    return is_array($translations) ? $translations : [];
}

function loadDemoReportData(string $jsonPath, string $locale): array
{
    if (! is_file($jsonPath)) {
        throw new RuntimeException("Missing demo JSON at [{$jsonPath}].");
    }

    $data = json_decode((string) file_get_contents($jsonPath), true);

    if (! is_array($data)) {
        throw new RuntimeException("Invalid demo JSON at [{$jsonPath}].");
    }

    return ReportDataNormalizer::normalize($data, $locale === 'en' ? 'en' : 'ro');
}

function renderSourcePdf(RemotePdfRenderer $renderer, array $variant): string
{
    $locale = $variant['locale'];
    $data = loadDemoReportData($variant['json'], $locale);
    $sourcePath = tempnam(sys_get_temp_dir(), 'landing-demo-rendered-');

    if ($sourcePath === false) {
        throw new RuntimeException('Unable to allocate a temporary file for rendered demo PDFs.');
    }

    try {
        $generatedAt = null;

        if (! empty($data['report_meta']['generated_at'])) {
            $generatedAt = date_create_immutable((string) $data['report_meta']['generated_at']) ?: null;
        }

        $renderer->saveView(
            $variant['template'],
            [
                'data' => $data,
                'locale' => $locale,
                'trans' => loadTranslations($locale),
            ],
            $sourcePath,
            basename($variant['output']),
            $generatedAt,
        );

        return $sourcePath;
    } catch (Throwable $exception) {
        @unlink($sourcePath);

        throw $exception;
    }
}

foreach ($variants as $variant) {
    $locale = $variant['locale'];
    $outputPath = $variant['output'];
    $sourceInputPath = null;
    $cleanupSourcePath = null;

    if ($variant['mode'] === 'existing') {
        $sourcePath = $variant['source'];

        if (! is_file($sourcePath)) {
            throw new RuntimeException("Missing source landing demo PDF at [{$sourcePath}].");
        }

        $sourceInputPath = tempnam(sys_get_temp_dir(), 'landing-demo-source-');

        if ($sourceInputPath === false) {
            throw new RuntimeException('Unable to allocate a temporary source file for landing demo PDF generation.');
        }

        copy($sourcePath, $sourceInputPath);
        $cleanupSourcePath = $sourceInputPath;
    } else {
        $sourceInputPath = renderSourcePdf($renderer, $variant);
        $cleanupSourcePath = $sourceInputPath;
    }

    $ctaUrl = LocalizedUrl::urlForLocale($locale, '/get-report?type=' . $variant['report_type']);

    $html = view('reports.landing-demo-cta', [
        'locale' => $locale,
        'ctaUrl' => $ctaUrl,
    ])->render();

    $ctaPdf = $renderer->renderHtml($html, '', "landing-demo-cta-{$locale}-{$variant['report_type']}.pdf");
    $ctaCopy = tempnam(sys_get_temp_dir(), 'landing-demo-cta-');

    if ($ctaCopy === false) {
        throw new RuntimeException('Unable to allocate a temporary CTA file for landing demo PDF generation.');
    }

    file_put_contents($ctaCopy, $ctaPdf);

    try {
        $pdf = new Fpdi();

        $sourcePageCount = $pdf->setSourceFile($sourceInputPath);
        $pagesToKeep = min(2, $sourcePageCount);

        for ($pageNo = 1; $pageNo <= $pagesToKeep; $pageNo++) {
            $template = $pdf->importPage($pageNo, PageBoundaries::CROP_BOX, true, true);
            $size = $pdf->getTemplateSize($template);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($template, 0, 0, $size['width'], $size['height'], true);
        }

        $pdf->setSourceFile($ctaCopy);
        $ctaTemplate = $pdf->importPage(1, PageBoundaries::CROP_BOX, true, true);
        $ctaSize = $pdf->getTemplateSize($ctaTemplate);

        $pdf->AddPage($ctaSize['orientation'], [$ctaSize['width'], $ctaSize['height']]);
        $pdf->useTemplate($ctaTemplate, 0, 0, $ctaSize['width'], $ctaSize['height'], true);

        $pdf->Output('F', $outputPath);
    } finally {
        if (is_string($cleanupSourcePath)) {
            @unlink($cleanupSourcePath);
        }
        @unlink($ctaCopy);
    }

    echo sprintf("Updated %s with the first two report pages plus a CTA page.%s", basename($outputPath), PHP_EOL);
}