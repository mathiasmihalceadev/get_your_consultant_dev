<?php

namespace App\Mail;

use App\Models\ReportPurchase;
use App\Models\SmartBillInvoice;
use App\Services\SmartBillService;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use RuntimeException;

class BillingTestInvoiceMail extends Mailable
{
    use SerializesModels;

    private ?string $cachedInvoicePdf = null;

    public function __construct(
        public ReportPurchase $purchase,
        public SmartBillInvoice $invoice,
    ) {
        if (!$this->purchase->relationLoaded('report') && $this->purchase->exists) {
            $this->purchase->load('report');
        }
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isRomanian()
                ? 'Factura ta de test SmartBill este pregatita'
                : 'Your SmartBill test invoice is ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.billing-test-invoice',
            with: [
                'purchase' => $this->purchase,
                'invoice' => $this->invoice,
                'isRomanian' => $this->isRomanian(),
                'documentNumber' => $this->documentNumber(),
                'recipientEmail' => $this->purchase->customer_email ?: $this->purchase->email ?: $this->purchase->report?->email,
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => $this->invoicePdfContent(),
                $this->invoiceAttachmentFilename(),
            )->withMime('application/pdf'),
        ];
    }

    private function invoicePdfContent(): string
    {
        if ($this->cachedInvoicePdf !== null) {
            return $this->cachedInvoicePdf;
        }

        $pdfContent = $this->smartBillService()->downloadInvoicePdf($this->invoice);

        if ($pdfContent === null || $pdfContent === '') {
            throw new RuntimeException('SmartBill invoice PDF is not available for the billing test email.');
        }

        $this->cachedInvoicePdf = $pdfContent;

        return $this->cachedInvoicePdf;
    }

    private function invoiceAttachmentFilename(): string
    {
        $series = $this->sanitizeFilenamePart($this->invoice->invoice_series ?: 'invoice');
        $number = $this->sanitizeFilenamePart($this->invoice->invoice_number ?: (string) ($this->invoice->id ?: 'document'));

        return sprintf('invoice-%s-%s.pdf', $series, $number);
    }

    private function documentNumber(): string
    {
        $series = trim((string) ($this->invoice->invoice_series ?? ''));
        $number = trim((string) ($this->invoice->invoice_number ?? ''));

        if ($series !== '' && $number !== '') {
            return $series . $number;
        }

        if ($series !== '') {
            return $series;
        }

        return $this->isRomanian() ? 'documentul SmartBill' : 'the SmartBill document';
    }

    private function sanitizeFilenamePart(string $value): string
    {
        $sanitized = preg_replace('/[^A-Za-z0-9._-]+/', '-', trim($value)) ?? '';
        $sanitized = trim($sanitized, '-._');

        return $sanitized !== '' ? $sanitized : 'document';
    }

    private function isRomanian(): bool
    {
        $locale = strtolower((string) ($this->purchase->locale ?: $this->purchase->report?->locale ?: 'en'));

        return $locale === 'ro';
    }

    private function smartBillService(): SmartBillService
    {
        $service = app(SmartBillService::class);

        if (!$service instanceof SmartBillService) {
            throw new RuntimeException('Unable to resolve SmartBillService from the container.');
        }

        return $service;
    }
}