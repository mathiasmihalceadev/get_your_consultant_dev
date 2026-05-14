<?php

namespace App\Jobs;

use App\Exceptions\OpenAIJsonException;
use App\Exceptions\OpenAIRequestException;
use App\Mail\ReportMail;
use App\Models\Report;
use App\Models\Settings;
use App\Support\ReportPdfFooter;
use App\Services\OpenAIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public function __construct(
        public int $reportId,
    ) {}

    public function handle(OpenAIService $openAI): void
    {
        $report = Report::find($this->reportId);

        if (!$report) {
            Log::channel('report')->warning('Report not found', ['report_id' => $this->reportId]);
            return;
        }

        Log::channel('report')->info('GenerateReportJob started', [
            'report_id' => $report->id,
            'type' => $report->report_type,
        ]);

        $locale = $report->locale === 'ro' ? 'ro' : 'en';
        $prompt = $this->resolvePrompt($report, $locale);

        try {
            $reportData = $openAI->generateReportData($report->url, $prompt);
        } catch (OpenAIJsonException|OpenAIRequestException $e) {
            Log::channel('report')->error('OpenAI request failed', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            $report->update([
                'status' => 'error',
                'error_message' => $e->getMessage(),
                'processed_at' => now(),
            ]);
            return;
        }

        // Generate PDF
        try {
            $dir = storage_path('app/public/reports');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $path = $dir . "/{$report->page_token}.pdf";

            $footerHtml = ReportPdfFooter::render(now());

            $templateView = $this->resolveTemplateView($report);

            Pdf::view($templateView, [
                'data' => $reportData,
                'report' => $report,
                'locale' => $locale,
                'trans' => $this->loadTranslations($locale),
            ])
                ->format('a4')
                ->withBrowsershot(function ($browsershot) use ($footerHtml) {
                    $browsershot->waitUntilNetworkIdle()
                        ->showBrowserHeaderAndFooter()
                        ->headerHtml('<div></div>')
                        ->footerHtml($footerHtml);
                })
                ->save($path);

            $report->report_url = "/storage/reports/{$report->page_token}.pdf";
        } catch (\Exception $e) {
            Log::channel('report')->error('PDF generation failed', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);
            $report->update([
                'status' => 'error',
                'error_message' => 'PDF generation failed: ' . $e->getMessage(),
                'processed_at' => now(),
            ]);
            return;
        }

        $report->setAttribute('processed_at', now());

        Log::channel('report')->info('PDF generated and stored', [
            'report_id' => $report->id,
            'path' => $report->report_url,
        ]);

        if (Settings::get('auto_send')) {
            Mail::to($report->email)->send(new ReportMail($report));
            $report->status = 'sent';
        } else {
            $report->status = 'to_be_sent';
        }

        $report->save();

        Log::channel('report')->info('GenerateReportJob completed', [
            'report_id' => $report->id,
            'status' => $report->status,
        ]);
    }

    private function loadTranslations(string $locale): array
    {
        $path = lang_path("{$locale}.json");
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true) ?? [];
        }
        return [];
    }

    private function resolvePrompt(Report $report, string $locale): string
    {
        $keys = $this->resolvePromptKeys($report, $locale);

        foreach ($keys as $key) {
            $value = Settings::get($key);

            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }

        throw new OpenAIRequestException('No report prompt is configured for the selected report type and locale.');
    }

    private function resolvePromptKeys(Report $report, string $locale): array
    {
        $localeSuffix = $locale === 'ro' ? 'ro' : 'eng';

        return match ($report->report_type) {
            'buying_living', 'buying_business' => array_values(array_filter([
                "buying_living_{$localeSuffix}",
                $locale === 'ro' ? 'buying_living_prompt_ro' : 'buying_living_prompt',
            ])),
            default => array_values(array_filter([
                "rental_living_{$localeSuffix}",
                $locale === 'ro' ? 'rental_living_prompt_ro' : 'rental_living_prompt',
            ])),
        };
    }

    private function resolveTemplateView(Report $report): string
    {
        return match ($report->report_type) {
            'buying_living', 'buying_business' => 'reports.template-buying',
            default => 'reports.template-rental',
        };
    }
}

