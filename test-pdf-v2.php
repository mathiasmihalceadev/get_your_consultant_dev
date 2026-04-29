<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\LaravelPdf\Facades\Pdf;

$data = json_decode(file_get_contents(storage_path('app/buying_ro.json')), true);
$path = storage_path('app/public/reports/test-buying.pdf');

$dir = dirname($path);
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$footerHtml = '<div style="width:100%;text-align:center;font-family:sans-serif;padding:0 40px;line-height:1.4;">'
    . '<div style="font-size:8px;color:#9CA3AF;font-style:italic;">Raport informativ generat prin analiza datelor publice disponibile și utilizarea unor modele statistice proprietare dezvoltate de Get Your Consultant.</div>'
    . '<div style="font-size:8px;color:#9CA3AF;font-style:italic;">Datele prezentate au caracter informativ și pot necesita verificare independentă.</div>'
    . '<div style="font-size:7px;color:#B0B0B0;margin-top:2px;">© 2026 Get Your Consultant. Toate drepturile rezervate.</div>'
    . '</div>';

Pdf::view('reports.template-buying', ['data' => $data])
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
