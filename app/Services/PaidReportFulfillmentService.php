<?php

namespace App\Services;

use App\Jobs\GenerateReportJob;
use App\Mail\ReportMail;
use App\Models\Report;
use App\Models\Settings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaidReportFulfillmentService
{
    public function fulfill(Report $report): void
    {
        if (in_array($report->status, ['pending', 'to_be_sent', 'sent'], true)) {
            Log::channel('report')->info('Paid report fulfillment skipped because the report is already processing or completed', [
                'report_id' => $report->id,
                'status' => $report->status,
            ]);

            return;
        }

        $existing = Report::query()
            ->where('url', $report->url)
            ->where('report_type', $report->report_type)
            ->where('id', '!=', $report->id)
            ->whereNotNull('report_url')
            ->whereIn('status', ['sent', 'to_be_sent'])
            ->first();

        if ($existing) {
            Log::channel('report')->info('Paid report matched an existing generated PDF', [
                'report_id' => $report->id,
                'existing_report_id' => $existing->id,
            ]);

            $report->update([
                'report_url' => $existing->report_url,
            ]);

            if (Settings::get('auto_send')) {
                Mail::to($report->email)->send(new ReportMail($report));

                $report->update([
                    'status' => 'sent',
                    'processed_at' => now(),
                ]);

                return;
            }

            $report->update([
                'status' => 'to_be_sent',
                'processed_at' => now(),
            ]);

            return;
        }

        $report->update([
            'status' => 'pending',
            'error_message' => null,
        ]);

        GenerateReportJob::dispatch($report->id);

        Log::channel('report')->info('GenerateReportJob dispatched after Stripe payment confirmation', [
            'report_id' => $report->id,
        ]);
    }
}