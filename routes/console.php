<?php

use App\Models\Report;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reports:migrate-private-storage', function () {
    $moved = 0;
    $updated = 0;
    $missing = 0;

    Report::query()
        ->orderBy('id')
        ->chunkById(100, function ($reports) use (&$moved, &$updated, &$missing): void {
            /** @var \Illuminate\Database\Eloquent\Collection<int, Report> $reports */
            foreach ($reports as $report) {
                /** @var Report $report */
                $privatePath = $report->pdfStoragePath();
                $legacyPath = $report->legacyPublicPdfStoragePath();
                $relativePath = $report->pdfStorageRelativePath();

                if (!is_file($privatePath) && is_file($legacyPath)) {
                    File::ensureDirectoryExists(dirname($privatePath));

                    if (!File::copy($legacyPath, $privatePath)) {
                        $this->warn("Could not copy report #{$report->id} to private storage.");
                        $missing++;
                        continue;
                    }

                    File::delete($legacyPath);
                    $moved++;
                }

                if (!is_file($privatePath)) {
                    $this->warn("Missing PDF for report #{$report->id}.");
                    $missing++;
                    continue;
                }

                if ($report->report_url !== $relativePath) {
                    $report->forceFill(['report_url' => $relativePath])->save();
                    $updated++;
                }
            }
        });

    $this->info("Private report migration completed. Moved: {$moved}. Updated: {$updated}. Missing: {$missing}.");
})->purpose('Move public report PDFs into private storage and normalize stored report paths');
