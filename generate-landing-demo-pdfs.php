<?php

declare(strict_types=1);

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
    'en' => [
        'source' => public_path('images/report-example-en.pdf'),
        'output' => public_path('images/report-example-en.pdf'),
        'url' => LocalizedUrl::urlForLocale('en', '/get-report?type=buying_living'),
    ],
    'ro' => [
        'source' => public_path('images/report-example-ro.pdf'),
        'output' => public_path('images/report-example-ro.pdf'),
        'url' => LocalizedUrl::urlForLocale('ro', '/get-report?type=buying_living'),
    ],
];

foreach ($variants as $locale => $variant) {
    $sourcePath = $variant['source'];
    $outputPath = $variant['output'];

    if (! is_file($sourcePath)) {
        throw new RuntimeException("Missing source landing demo PDF at [{$sourcePath}].");
    }

    $html = view('reports.landing-demo-cta', [
        'locale' => $locale,
        'ctaUrl' => $variant['url'],
    ])->render();

    $ctaPdf = $renderer->renderHtml($html, '', "landing-demo-cta-{$locale}.pdf");

    $sourceCopy = tempnam(sys_get_temp_dir(), 'landing-demo-source-');
    $ctaCopy = tempnam(sys_get_temp_dir(), 'landing-demo-cta-');

    if ($sourceCopy === false || $ctaCopy === false) {
        throw new RuntimeException('Unable to allocate temporary files for landing demo PDF generation.');
    }

    copy($sourcePath, $sourceCopy);
    file_put_contents($ctaCopy, $ctaPdf);

    try {
        $pdf = new Fpdi();

        $sourcePageCount = $pdf->setSourceFile($sourceCopy);
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
        @unlink($sourceCopy);
        @unlink($ctaCopy);
    }

    echo sprintf("Updated %s with the current first two pages plus a CTA page.%s", basename($outputPath), PHP_EOL);
}