<?php

namespace App\Jobs;

use App\Exceptions\SmartBillException;
use App\Mail\BillingTestInvoiceMail;
use App\Models\ReportPurchase;
use App\Models\SmartBillInvoice;
use App\Services\SmartBillService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SyncSmartBillInvoiceJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [60, 300, 900];

    public $uniqueFor = 3600;

    public function __construct(
        public int $purchaseId,
    ) {
    }

    public function uniqueId(): string
    {
        return (string) $this->purchaseId;
    }

    public function handle(SmartBillService $smartBill): void
    {
        $purchase = ReportPurchase::with(['report', 'smartBillInvoice'])->find($this->purchaseId);

        if (!$purchase) {
            Log::channel('smartbill')->warning('SmartBill sync skipped because purchase was not found', [
                'purchase_id' => $this->purchaseId,
            ]);

            return;
        }

        if ($purchase->paid_at === null && $purchase->status !== 'paid') {
            Log::channel('smartbill')->info('SmartBill sync skipped because purchase is not marked as paid', [
                'purchase_id' => $purchase->id,
                'status' => $purchase->status,
            ]);

            return;
        }

        if (
            $purchase->smartBillInvoice
            && $purchase->smartBillInvoice->status === 'completed'
            && $purchase->smartBillInvoice->payment_status === 'registered'
        ) {
            $this->sendTestInvoiceEmailIfRequested($purchase, $purchase->smartBillInvoice);

            Log::channel('smartbill')->info('SmartBill sync skipped because invoice is already completed', [
                'purchase_id' => $purchase->id,
                'smart_bill_invoice_id' => $purchase->smartBillInvoice->id,
            ]);

            return;
        }

        try {
            $invoice = $smartBill->syncPurchase($purchase);

            $this->markTestReportAsCompleted($purchase);
            $this->sendTestInvoiceEmailIfRequested($purchase->fresh(['report']), $invoice);
        } catch (SmartBillException $exception) {
            $this->markTestReportAsFailed($purchase, $exception->getMessage());

            if ($exception->retryable) {
                throw $exception;
            }

            return;
        }
    }

    public function failed(?\Throwable $exception): void
    {
        if (!$exception) {
            return;
        }

        $purchase = ReportPurchase::find($this->purchaseId);

        if (!$purchase) {
            return;
        }

        $invoice = SmartBillInvoice::firstOrCreate(
            ['report_purchase_id' => $purchase->id],
            [
                'report_id' => $purchase->report_id,
                'status' => 'pending',
                'payment_status' => 'pending',
            ],
        );

        $invoice->forceFill([
            'status' => $invoice->invoice_number ? 'issued' : 'failed',
            'payment_status' => $invoice->invoice_number && $invoice->payment_status !== 'registered'
                ? 'failed'
                : $invoice->payment_status,
            'last_attempt_at' => now(),
            'error_message' => $exception->getMessage(),
        ])->save();

        Log::channel('smartbill')->error('SmartBill sync failed permanently', [
            'purchase_id' => $purchase->id,
            'report_id' => $purchase->report_id,
            'smart_bill_invoice_id' => $invoice->id,
            'error' => $exception->getMessage(),
        ]);

        $this->markTestReportAsFailed($purchase, $exception->getMessage());
    }

    private function markTestReportAsCompleted(ReportPurchase $purchase): void
    {
        if (!$purchase->report?->is_test) {
            return;
        }

        $purchase->report->update([
            'status' => 'test_completed',
            'processed_at' => now(),
            'error_message' => null,
        ]);
    }

    private function markTestReportAsFailed(ReportPurchase $purchase, string $message): void
    {
        if (!$purchase->report?->is_test) {
            return;
        }

        $purchase->report->update([
            'status' => 'error',
            'processed_at' => now(),
            'error_message' => $message,
        ]);
    }

    private function sendTestInvoiceEmailIfRequested(ReportPurchase $purchase, SmartBillInvoice $invoice): void
    {
        if (!$purchase->report?->is_test) {
            return;
        }

        $metadata = is_array($purchase->metadata) ? $purchase->metadata : [];
        $sendRequested = filter_var(
            $metadata['send_test_invoice_email'] ?? false,
            FILTER_VALIDATE_BOOL,
        );
        $sentAt = trim((string) ($metadata['test_invoice_email_sent_at'] ?? ''));

        if (!$sendRequested || $sentAt !== '') {
            return;
        }

        $recipient = $purchase->customer_email ?: $purchase->email ?: $purchase->report?->email;

        if (!$recipient) {
            Log::channel('report')->warning('Billing test invoice email skipped because no recipient email is available', [
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
                'smart_bill_invoice_id' => $invoice->id,
            ]);

            return;
        }

        try {
            Mail::to($recipient)->send(new BillingTestInvoiceMail($purchase, $invoice));

            $metadata['test_invoice_email_sent_at'] = now()->toIso8601String();
            unset($metadata['test_invoice_email_last_error']);

            $purchase->forceFill(['metadata' => $metadata])->save();

            Log::channel('report')->info('Billing test invoice email sent', [
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
                'smart_bill_invoice_id' => $invoice->id,
                'email' => $recipient,
            ]);
        } catch (\Throwable $exception) {
            $metadata['test_invoice_email_last_error'] = $exception->getMessage();

            $purchase->forceFill(['metadata' => $metadata])->save();

            Log::channel('report')->warning('Billing test invoice email failed', [
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
                'smart_bill_invoice_id' => $invoice->id,
                'email' => $recipient,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}