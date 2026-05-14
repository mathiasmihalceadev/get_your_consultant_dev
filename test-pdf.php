<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\LaravelPdf\Facades\Pdf;
use App\Support\ReportPdfFooter;

$data = json_decode(file_get_contents(storage_path('app/rental_ro.json')), true);
$path = storage_path('app/public/reports/test-rental.pdf');

$dir = dirname($path);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$footerHtml = ReportPdfFooter::render(now());

Pdf::view('reports.template-rental', ['data' => $data, 'locale' => 'ro'])
    ->format('a4')
    ->withBrowsershot(function ($bs) use ($footerHtml) {
        $bs->waitUntilNetworkIdle()
           ->showBrowserHeaderAndFooter()
           ->headerHtml('<div></div>')
           ->footerHtml($footerHtml);
    })
    ->save($path);

echo "PDF saved to: $path\n";
echo "Size: " . filesize($path) . " bytes\n";
