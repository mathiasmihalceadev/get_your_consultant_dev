<?php

namespace Tests\Unit;

use App\Exceptions\SmartBillException;
use App\Mail\BillingTestInvoiceMail;
use App\Models\Report;
use App\Models\ReportPurchase;
use App\Models\SmartBillInvoice;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BillingTestInvoiceMailTest extends TestCase
{
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

    public function test_it_attaches_only_the_smartbill_invoice_pdf_for_billing_test_emails(): void
    {
        $invoicePdf = "%PDF-invoice\n";
        [$purchase, $invoice] = $this->makePurchaseAndInvoice('ro', 'buyer@example.com', 'FCT', '0023');

        Http::fake([
            'https://smartbill.test/SBORO/api/invoice/pdf?*' => Http::response(
                base64_encode($invoicePdf),
                200,
                ['Content-Type' => 'application/octet-stream'],
            ),
        ]);

        $mail = new BillingTestInvoiceMail($purchase, $invoice);
        $attachments = $mail->attachments();

        $this->assertSame('Factura ta de test SmartBill este pregatita', $mail->envelope()->subject);
        $this->assertCount(1, $attachments);
        $this->assertTrue(
            $attachments[0]->isEquivalent(
                Attachment::fromData(fn () => $invoicePdf, 'invoice-FCT-0023.pdf')
                    ->withMime('application/pdf')
            )
        );
    }

    public function test_it_fails_when_the_smartbill_invoice_pdf_cannot_be_downloaded(): void
    {
        [$purchase, $invoice] = $this->makePurchaseAndInvoice('en', 'buyer@example.com', 'FCT', '0024');

        Http::fake([
            'https://smartbill.test/SBORO/api/invoice/pdf?*' => Http::response([
                'errorText' => 'Factura nu a fost gasita!',
            ], 404),
        ]);

        $mail = new BillingTestInvoiceMail($purchase, $invoice);
        $attachment = $mail->attachments()[0];

        $this->expectException(SmartBillException::class);

        $attachment->isEquivalent(
            Attachment::fromData(fn () => "%PDF-invoice\n", 'invoice-FCT-0024.pdf')
                ->withMime('application/pdf')
        );
    }

    /**
     * @return array{0: ReportPurchase, 1: SmartBillInvoice}
     */
    private function makePurchaseAndInvoice(
        string $locale,
        string $email,
        string $series,
        string $number,
    ): array {
        $report = (new Report())->forceFill([
            'id' => 31,
            'report_type' => 'buying_living',
            'email' => $email,
            'locale' => $locale,
            'is_test' => true,
            'page_token' => 'test-token',
        ]);

        $purchase = (new ReportPurchase())->forceFill([
            'id' => 41,
            'report_id' => 31,
            'locale' => $locale,
            'email' => $email,
            'customer_email' => $email,
        ]);
        $purchase->setRelation('report', $report);

        $invoice = (new SmartBillInvoice())->forceFill([
            'id' => 51,
            'report_id' => 31,
            'report_purchase_id' => 41,
            'invoice_series' => $series,
            'invoice_number' => $number,
        ]);

        return [$purchase, $invoice];
    }
}