<?php

namespace Tests\Unit;

use App\Models\Report;
use App\Models\ReportPurchase;
use App\Models\SmartBillInvoice;
use App\Services\SmartBillService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmartBillServiceTest extends TestCase
{
    use RefreshDatabase;

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
                'tax_name' => 'Normala',
                'tax_percentage' => 19,
                'tax_included' => true,
            ],
        ]);
    }

    public function test_it_issues_a_smartbill_invoice_and_registers_the_payment(): void
    {
        Http::fake([
            'https://smartbill.test/SBORO/api/invoice' => Http::response([
                'sbcResponse' => [
                    'errorText' => '',
                    'message' => '',
                    'number' => '0023',
                    'series' => 'FCT',
                    'documentUrl' => 'https://cloud.smartbill.ro/documente/editare/factura/274119/',
                    'documentId' => '274119',
                    'documentViewUrl' => 'https://cloud.smartbill.ro/documente/extern/pf/factura/token',
                ],
            ], 200),
            'https://smartbill.test/SBORO/api/payment' => Http::response([
                'sbcResponse' => [
                    'errorText' => '',
                    'message' => 'Payment registered',
                    'number' => '',
                    'series' => '',
                ],
            ], 200),
        ]);

        $purchase = $this->createPaidPurchase();

        $invoice = app(SmartBillService::class)->syncPurchase($purchase);

        $this->assertSame('completed', $invoice->status);
        $this->assertSame('registered', $invoice->payment_status);
        $this->assertSame('FCT', $invoice->invoice_series);
        $this->assertSame('0023', $invoice->invoice_number);
        $this->assertSame('https://cloud.smartbill.ro/documente/extern/pf/factura/token', $invoice->file_url);
        $this->assertSame('Card online', $invoice->payment_type);
        $this->assertNotNull($invoice->issued_at);
        $this->assertNotNull($invoice->payment_registered_at);

        Http::assertSentCount(2);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://smartbill.test/SBORO/api/invoice'
                && $request['companyVatCode'] === 'RO12345678'
                && $request['seriesName'] === 'FCT'
                && $request['client']['name'] === 'Ion Popescu'
                && $request['client']['vatCode'] === '-'
                && $request['products'][0]['code'] === 'REPORT-BUYING-LIVING'
                && $request['products'][0]['name'] === 'Raport cumparare locuinta'
                && $request['products'][0]['isService'] === true
                && $request['products'][0]['price'] === 119.0;
        });
        Http::assertSent(function ($request) {
            return $request->url() === 'https://smartbill.test/SBORO/api/payment'
                && $request['issueDate'] === now()->format('Y-m-d')
                && $request['type'] === 'Card online'
                && $request['useInvoiceDetails'] === true
                && $request['invoicesList'][0]['seriesName'] === 'FCT'
                && $request['invoicesList'][0]['number'] === '0023';
        });
    }

    public function test_it_reuses_an_existing_invoice_and_only_retries_payment_registration(): void
    {
        Http::fake([
            'https://smartbill.test/SBORO/api/payment' => Http::response([
                'sbcResponse' => [
                    'errorText' => '',
                    'message' => 'Payment registered',
                    'number' => '',
                    'series' => '',
                ],
            ], 200),
        ]);

        $purchase = $this->createPaidPurchase();
        $existingInvoice = SmartBillInvoice::create([
            'report_id' => $purchase->report_id,
            'report_purchase_id' => $purchase->id,
            'status' => 'issued',
            'payment_status' => 'failed',
            'company_vat_code' => 'RO12345678',
            'invoice_series' => 'FCT',
            'invoice_number' => '0023',
            'file_url' => 'https://cloud.smartbill.ro/documente/extern/pf/factura/token',
        ]);

        $invoice = app(SmartBillService::class)->syncPurchase($purchase);

        $this->assertSame($existingInvoice->id, $invoice->id);
        $this->assertSame('completed', $invoice->status);
        $this->assertSame('registered', $invoice->payment_status);

        Http::assertSentCount(1);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://smartbill.test/SBORO/api/payment'
                && $request['invoicesList'][0]['seriesName'] === 'FCT'
                && $request['invoicesList'][0]['number'] === '0023';
        });
        Http::assertNotSent(function ($request) {
            return $request->url() === 'https://smartbill.test/SBORO/api/invoice';
        });
    }

    public function test_it_transfers_collected_tax_ids_to_smartbill_client_payload(): void
    {
        Http::fake([
            'https://smartbill.test/SBORO/api/invoice' => Http::response([
                'sbcResponse' => [
                    'errorText' => '',
                    'message' => '',
                    'number' => '0024',
                    'series' => 'FCT',
                    'documentUrl' => 'https://cloud.smartbill.ro/documente/editare/factura/274120/',
                    'documentId' => '274120',
                    'documentViewUrl' => 'https://cloud.smartbill.ro/documente/extern/pf/factura/token-2',
                ],
            ], 200),
            'https://smartbill.test/SBORO/api/payment' => Http::response([
                'sbcResponse' => [
                    'errorText' => '',
                    'message' => 'Payment registered',
                    'number' => '',
                    'series' => '',
                ],
            ], 200),
        ]);

        $purchase = $this->createPaidCompanyPurchase();

        app(SmartBillService::class)->syncPurchase($purchase);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://smartbill.test/SBORO/api/invoice'
                && $request['client']['name'] === 'ACME Imobiliare SRL'
                && $request['client']['vatCode'] === 'RO12345678'
                && $request['client']['isTaxPayer'] === true;
        });
    }

    private function createPaidPurchase(): ReportPurchase
    {
        $report = Report::create([
            'report_type' => 'buying_living',
            'url' => 'https://example.com/property/1',
            'email' => 'buyer@example.com',
            'locale' => 'ro',
            'status' => 'payment_processing',
            'page_token' => 'token-123',
        ]);

        return ReportPurchase::create([
            'report_id' => $report->id,
            'report_type' => $report->report_type,
            'locale' => 'ro',
            'email' => 'buyer@example.com',
            'status' => 'paid',
            'amount_subtotal' => 11900,
            'amount_total' => 11900,
            'currency' => 'ron',
            'paid_currency' => 'ron',
            'base_currency' => 'eur',
            'base_amount_minor' => 2400,
            'checkout_amount_minor' => 11900,
            'exchange_rate' => 4.95,
            'customer_email' => 'buyer@example.com',
            'customer_name' => 'Ion Popescu',
            'customer_phone' => '0712345678',
            'customer_address' => [
                'line1' => 'Strada Sperantei 5',
                'city' => 'Sector 1',
                'state' => 'Bucuresti',
                'postal_code' => '010101',
                'country' => 'RO',
            ],
            'customer_details' => [
                'email' => 'buyer@example.com',
                'name' => 'Ion Popescu',
                'phone' => '0712345678',
                'address' => [
                    'line1' => 'Strada Sperantei 5',
                    'city' => 'Sector 1',
                    'state' => 'Bucuresti',
                    'postal_code' => '010101',
                    'country' => 'RO',
                ],
            ],
            'paid_at' => now(),
        ]);
    }

    private function createPaidCompanyPurchase(): ReportPurchase
    {
        $report = Report::create([
            'report_type' => 'buying_living',
            'url' => 'https://example.com/property/company',
            'email' => 'office@acme.test',
            'locale' => 'ro',
            'status' => 'payment_processing',
            'page_token' => 'token-company',
        ]);

        return ReportPurchase::create([
            'report_id' => $report->id,
            'report_type' => $report->report_type,
            'locale' => 'ro',
            'email' => 'office@acme.test',
            'status' => 'paid',
            'amount_subtotal' => 11900,
            'amount_total' => 11900,
            'currency' => 'ron',
            'paid_currency' => 'ron',
            'base_currency' => 'eur',
            'base_amount_minor' => 2400,
            'checkout_amount_minor' => 11900,
            'exchange_rate' => 4.95,
            'customer_email' => 'office@acme.test',
            'customer_name' => 'ACME Imobiliare SRL',
            'customer_phone' => '0711223344',
            'customer_address' => [
                'line1' => 'Bulevardul Unirii 10',
                'city' => 'Bucuresti',
                'state' => 'Bucuresti',
                'postal_code' => '030123',
                'country' => 'RO',
            ],
            'customer_details' => [
                'email' => 'office@acme.test',
                'name' => 'ACME Imobiliare SRL',
                'business_name' => 'ACME Imobiliare SRL',
                'phone' => '0711223344',
                'address' => [
                    'line1' => 'Bulevardul Unirii 10',
                    'city' => 'Bucuresti',
                    'state' => 'Bucuresti',
                    'postal_code' => '030123',
                    'country' => 'RO',
                ],
                'tax_ids' => [
                    [
                        'type' => 'eu_vat',
                        'value' => 'RO12345678',
                    ],
                ],
            ],
            'paid_at' => now(),
        ]);
    }
}