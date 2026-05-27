<?php

namespace Tests\Unit;

use App\Mail\ReportMail;
use App\Models\Report;
use App\Models\ReportPurchase;
use App\Models\SmartBillInvoice;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ReportMailTest extends TestCase
{
    /**
     * @var array<int, string>
     */
    private array $createdFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.smartbill', [
            'username' => 'billing@example.com',
            'token' => 'smartbill-token',
            'company_vat_code' => 'RO12345678',
            'base_url' => 'https://smartbill.test/SBORO/api',
            'timeout' => 10,
            'invoice' => [
                'series' => 'FCT',
                'payment_type' => 'Card online',
                'test_draft' => true,
                'tax_name' => 'Normala',
                'tax_percentage' => 19,
                'tax_included' => true,
            ],
        ]);
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_it_attaches_the_report_pdf_and_invoice_pdf_when_available(): void
    {
        $reportPdf = "%PDF-report\n";
        $invoicePdf = "%PDF-invoice\n";
        $report = $this->makeReportWithInvoice('report-mail-test.pdf', $this->makeInvoice('FCT', '0023'));

        $this->writeReportPdf($report, $reportPdf);

        Http::fake([
            'https://smartbill.test/SBORO/api/invoice/pdf?*' => Http::response(
                base64_encode($invoicePdf),
                200,
                ['Content-Type' => 'application/octet-stream'],
            ),
        ]);

        $attachments = (new ReportMail($report))->attachments();

        $this->assertCount(2, $attachments);
        $this->assertTrue(
            $attachments[0]->isEquivalent(
                Attachment::fromPath($report->pdfStoragePath())
                    ->as('report-mail-test.pdf')
                    ->withMime('application/pdf')
            )
        );
        $this->assertTrue(
            $attachments[1]->isEquivalent(
                Attachment::fromData(fn () => $invoicePdf, 'invoice-FCT-0023.pdf')
                    ->withMime('application/pdf')
            )
        );

        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request->url() === 'https://smartbill.test/SBORO/api/invoice/pdf?cif=RO12345678&seriesname=FCT&number=0023');
    }

    public function test_it_still_attaches_the_report_pdf_when_invoice_download_fails(): void
    {
        $reportPdf = "%PDF-report\n";
        $report = $this->makeReportWithInvoice('report-mail-fallback.pdf', $this->makeInvoice('FCT', '0024'));

        $this->writeReportPdf($report, $reportPdf);

        Http::fake([
            'https://smartbill.test/SBORO/api/invoice/pdf?*' => Http::response([
                'errorText' => 'Factura nu a fost gasita!',
            ], 404),
        ]);

        $attachments = (new ReportMail($report))->attachments();

        $this->assertCount(1, $attachments);
        $this->assertTrue(
            $attachments[0]->isEquivalent(
                Attachment::fromPath($report->pdfStoragePath())
                    ->as('report-mail-fallback.pdf')
                    ->withMime('application/pdf')
            )
        );

        Http::assertSentCount(1);
    }

    private function makeReportWithInvoice(string $filename, SmartBillInvoice $invoice): Report
    {
        $purchase = (new ReportPurchase())->forceFill([
            'id' => 21,
            'report_id' => 15,
        ]);
        $purchase->setRelation('smartBillInvoice', $invoice);

        $report = (new Report())->forceFill([
            'id' => 15,
            'report_type' => 'buying_living',
            'email' => 'buyer@example.com',
            'locale' => 'ro',
            'page_token' => 'token-123',
            'report_url' => '/storage/reports/' . $filename,
        ]);
        $report->setRelation('latestPurchase', $purchase);

        return $report;
    }

    private function makeInvoice(string $series, string $number): SmartBillInvoice
    {
        return (new SmartBillInvoice())->forceFill([
            'id' => 91,
            'report_id' => 15,
            'report_purchase_id' => 21,
            'invoice_series' => $series,
            'invoice_number' => $number,
        ]);
    }

    private function writeReportPdf(Report $report, string $contents): void
    {
        $path = $report->pdfStoragePath();
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents);

        $this->createdFiles[] = $path;
    }
}