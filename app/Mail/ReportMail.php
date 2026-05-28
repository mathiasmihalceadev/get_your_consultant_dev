<?php

namespace App\Mail;

use App\Models\Report;
use App\Models\SmartBillInvoice;
use App\Services\SmartBillService;
use App\Support\LocalizedUrl;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ReportMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Report $report,
    ) {}

    public function envelope(): Envelope
    {
        $locale = $this->report->locale ?? 'en';
        $translations = $this->loadTranslations($locale);

        $subjectKey = "email_subject_{$this->report->report_type}";
        $subject = $translations[$subjectKey] ?? "Your Property Report is Ready";

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        $locale = $this->report->locale ?? 'en';
        $publicLocale = LocalizedUrl::publicLocale($locale);
        $translations = $this->loadTranslations($locale);

        $typeKey = "type_{$this->report->report_type}";
        $typeLabel = $translations[$typeKey] ?? $this->report->report_type;
        $statusUrl = LocalizedUrl::publicUrlForLocale($locale, "/report/{$this->report->page_token}");
        $downloadPath = $this->report->report_url ?: $this->report->pdfPublicUrl();

        return new Content(
            view: 'emails.report',
            with: [
                'report' => $this->report,
                'typeLabel' => $typeLabel,
                'downloadUrl' => LocalizedUrl::publicUrlForLocale($locale, $downloadPath),
                'statusUrl' => $statusUrl,
                'contactUrl' => LocalizedUrl::publicUrlForLocale($locale, '/contact'),
                'logoUrl' => LocalizedUrl::publicUrlForLocale($locale, '/images/main-logo-transparent.png'),
                'contactEmail' => $publicLocale === 'ro'
                    ? 'contact@getyourconsultant.ro'
                    : 'contact@getyourconsultant.com',
                'websiteUrl' => LocalizedUrl::publicUrlForLocale($locale, '/'),
                'websiteLabel' => $publicLocale === 'ro'
                    ? 'getyourconsultant.ro'
                    : 'getyourconsultant.com',
                'currentYear' => now()->year,
                'trans' => $translations,
            ],
        );
    }

    public function attachments(): array
    {
        return array_values(array_filter([
            $this->reportPdfAttachment(),
            $this->invoicePdfAttachment(),
        ]));
    }

    private function reportPdfAttachment(): ?Attachment
    {
        $path = $this->report->pdfStoragePath();

        if (!is_file($path)) {
            return null;
        }

        return Attachment::fromPath($path)
            ->as($this->reportAttachmentFilename())
            ->withMime('application/pdf');
    }

    private function invoicePdfAttachment(): ?Attachment
    {
        $invoice = $this->latestSmartBillInvoice();

        if (!$invoice) {
            return null;
        }

        try {
            $pdfContent = $this->smartBillService()->downloadInvoicePdf($invoice);
        } catch (Throwable $exception) {
            Log::channel('smartbill')->warning('Skipping SmartBill invoice email attachment', [
                'report_id' => $this->report->id,
                'smart_bill_invoice_id' => $invoice->id,
                'invoice_series' => $invoice->invoice_series,
                'invoice_number' => $invoice->invoice_number,
                'error' => $exception->getMessage(),
            ]);

            return null;
        }

        if ($pdfContent === null || $pdfContent === '') {
            return null;
        }

        return Attachment::fromData(
            fn () => $pdfContent,
            $this->invoiceAttachmentFilename($invoice),
        )->withMime('application/pdf');
    }

    private function latestSmartBillInvoice(): ?SmartBillInvoice
    {
        $purchase = $this->report->relationLoaded('latestPurchase')
            ? $this->report->getRelation('latestPurchase')
            : ($this->report->exists
                ? $this->report->latestPurchase()->with('smartBillInvoice')->first()
                : null);

        if ($purchase !== null) {
            if (!$purchase->relationLoaded('smartBillInvoice')) {
                $purchase->load('smartBillInvoice');
            }

            if ($purchase->smartBillInvoice !== null) {
                return $purchase->smartBillInvoice;
            }
        }

        if ($this->report->relationLoaded('smartBillInvoices')) {
            return $this->report->smartBillInvoices->sortByDesc('id')->first();
        }

        return $this->report->exists
            ? $this->report->smartBillInvoices()->latest('id')->first()
            : null;
    }

    private function reportAttachmentFilename(): string
    {
        $locale = strtolower((string) ($this->report->locale ?? 'en'));

        return $locale === 'ro' ? 'raport.pdf' : 'report.pdf';
    }

    private function invoiceAttachmentFilename(SmartBillInvoice $invoice): string
    {
        $series = $this->sanitizeFilenamePart($invoice->invoice_series ?: 'invoice');
        $number = $this->sanitizeFilenamePart($invoice->invoice_number ?: (string) ($invoice->id ?: 'document'));

        return sprintf('invoice-%s-%s.pdf', $series, $number);
    }

    private function sanitizeFilenamePart(string $value): string
    {
        $sanitized = preg_replace('/[^A-Za-z0-9._-]+/', '-', trim($value)) ?? '';
        $sanitized = trim($sanitized, '-._');

        return $sanitized !== '' ? $sanitized : 'document';
    }

    private function smartBillService(): SmartBillService
    {
        $service = app(SmartBillService::class);

        if (!$service instanceof SmartBillService) {
            throw new \RuntimeException('Unable to resolve SmartBillService from the container.');
        }

        return $service;
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
