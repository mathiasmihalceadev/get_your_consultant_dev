<?php

namespace App\Jobs;

use App\Exceptions\OpenAIJsonException;
use App\Exceptions\OpenAIRequestException;
use App\Mail\ReportMail;
use App\Models\Report;
use App\Models\Settings;
use App\Services\OpenAIService;
use App\Services\RemotePdfRenderer;
use App\Support\ReportDataNormalizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;

    public function __construct(
        public int $reportId,
    ) {}

    public function handle(OpenAIService $openAI, RemotePdfRenderer $pdfRenderer): void
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
            $reportData = ReportDataNormalizer::normalize($reportData, $locale);
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
            $dir = dirname($report->pdfStoragePath());
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = $report->pdfStorageFilename();
            $path = $report->pdfStoragePath();

            $templateView = $this->resolveTemplateView($report);

            $pdfRenderer->saveView($templateView, [
                'data' => $reportData,
                'report' => $report,
                'locale' => $locale,
                'trans' => $this->loadTranslations($locale),
            ], $path, $filename, now());

            $report->report_url = $report->pdfStorageRelativePath();
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
                return $this->appendRenderingConstraints($value, $locale);
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

    private function appendRenderingConstraints(string $prompt, string $locale): string
    {
        $oneWordExamples = $locale === 'en'
            ? 'Price, Notary, Registry, Agency, Bank, Renovation, Reserve'
            : 'Preț, Notar, Carte, Comision, Bancă, Renovare, Rezervă';

        return rtrim($prompt) . "\n\nAdditional hard output constraints:\n"
            . "- page_one.badges must fit visually within 2 rows on the first page. Keep only the most decision-relevant badges and never exceed 8 badges.\n"
            . "- page_one.verdict.ideal_for must contain at most 3 short items.\n"
            . "- page_one.verdict.not_ideal_for must contain at most 3 short items.\n"
            . "- page_one.verdict.one_liner must stay concise and scannable.\n"
            . "- If the chart id is total_acquisition_cost, every data.segments[].label must be a single word. Use labels like: {$oneWordExamples}.\n";
    }
}

