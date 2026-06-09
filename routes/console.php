<?php

use App\Models\Report;
use App\Services\ReportFeedbackService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;

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

Artisan::command('reports:send-feedback-emails {--dry-run : Count matching reports without sending emails}', function (ReportFeedbackService $feedback) {
    $query = Report::query()
        ->where('is_test', false)
        ->whereNull('feedback_sent_at')
        ->whereNotNull('email')
        ->whereNotNull('page_token')
        ->whereNotNull('processed_at')
        ->where('processed_at', '<=', now()->subMinutes(30))
        ->whereIn('status', ['to_be_sent', 'sent'])
        ->orderBy('id');

    if ($this->option('dry-run')) {
        $this->info('Feedback emails pending: '.$query->count());

        return Command::SUCCESS;
    }

    $sent = 0;
    $skipped = 0;
    $failed = 0;

    $query->chunkById(100, function ($reports) use ($feedback, &$sent, &$skipped, &$failed): void {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Report> $reports */
        foreach ($reports as $report) {
            /** @var Report $report */
            try {
                if ($feedback->send($report)) {
                    $sent++;
                } else {
                    $skipped++;
                }
            } catch (\Throwable $e) {
                $failed++;
                $this->warn("Feedback email failed for report #{$report->id}: {$e->getMessage()}");
            }
        }
    });

    $this->info("Feedback email run completed. Sent: {$sent}. Skipped: {$skipped}. Failed: {$failed}.");

    return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
})->purpose('Send feedback emails for generated reports older than 30 minutes');
