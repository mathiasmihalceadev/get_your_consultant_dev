<?php

namespace App\Jobs;

use App\Exceptions\OpenAIJsonException;
use App\Exceptions\OpenAIRequestException;
use App\Mail\ReportMail;
use App\Models\Report;
use App\Models\Settings;
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

        $locale = $report->locale ?? 'en';
        $suffix = $locale === 'ro' ? '_ro' : '';

        $baseField = match ($report->report_type) {
            'rental_living' => 'rental_living_prompt',
            'rental_business' => 'rental_business_prompt',
            'buying_living' => 'buying_living_prompt',
            'buying_business' => 'buying_business_prompt',
        };

        $prompt = Settings::get($baseField . $suffix) ?? Settings::get($baseField);

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

            $footerHtml = '<div style="width:100%;text-align:center;font-family:Inter,sans-serif;padding:0 40px;line-height:1.4;">'
                . '<div style="font-size:8px;color:#9CA3AF;font-style:italic;">Raport informativ generat prin analiza datelor publice disponibile È™i utilizarea unor modele statistice proprietare dezvoltate de Get Your Consultant.</div>'
                . '<div style="font-size:8px;color:#9CA3AF;font-style:italic;">Datele prezentate au caracter informativ È™i pot necesita verificare independentÄƒ.</div>'
                . '<div style="font-size:7px;color:#B0B0B0;margin-top:2px;">Â© 2026 Get Your Consultant. Toate drepturile rezervate.</div>'
                . '</div>';

            $reportType = $reportData['report_meta']['report_type'] ?? 'rental';
            $templateView = $reportType === 'buying' ? 'reports.template-buying' : 'reports.template-rental';

            Pdf::view($templateView, ['data' => $reportData, 'report' => $report, 'trans' => $this->loadTranslations($report->locale ?? 'en')])
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

        $report->processed_at = now();

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
}

