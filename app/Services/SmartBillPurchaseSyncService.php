<?php

namespace App\Services;

use App\Exceptions\SmartBillException;
use App\Mail\BillingTestInvoiceMail;
use App\Models\ReportPurchase;
use App\Models\SmartBillInvoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SmartBillPurchaseSyncService
{
    public function __construct(
        private readonly SmartBillService $smartBill,
    ) {
    }

    public function syncPaidPurchase(ReportPurchase $purchase): void
    {
        $purchase->loadMissing(['report', 'smartBillInvoice']);

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
            $invoice = $this->smartBill->syncPurchase($purchase);

            $this->markTestReportAsCompleted($purchase);
            $this->sendTestInvoiceEmailIfRequested($this->refreshPurchaseForEmail($purchase), $invoice);
        } catch (SmartBillException $exception) {
            Log::channel('smartbill')->warning('Synchronous SmartBill sync failed', [
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
                'retryable' => $exception->retryable,
                'error' => $exception->getMessage(),
            ]);

            $this->markTestReportAsFailed($purchase, $exception->getMessage());
        }
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

    private function refreshPurchaseForEmail(ReportPurchase $purchase): ReportPurchase
    {
        if (!$purchase->exists) {
            return $purchase;
        }

        return $purchase->fresh(['report']) ?? $purchase;
    }
}