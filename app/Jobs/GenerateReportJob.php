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

        $settings = Settings::first();

        $prompt = match ($report->report_type) {
            'purchase' => $settings->purchase_prompt,
            'rental' => $settings->rental_prompt,
            'commercial' => $settings->commercial_prompt,
        };

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
            Storage::makeDirectory('reports');
            $path = storage_path("app/reports/{$report->page_token}.pdf");

            Pdf::view('reports.template', ['data' => $reportData, 'report' => $report])
                ->format('a4')
                ->save($path);

            $report->report_url = "reports/{$report->page_token}.pdf";
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

        if ($settings->auto_send) {
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
}
