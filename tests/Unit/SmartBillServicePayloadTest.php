<?php

namespace Tests\Unit;

use App\Models\ReportPurchase;
use App\Models\SmartBillInvoice;
use App\Services\SmartBillService;
use Tests\TestCase;

class SmartBillServicePayloadTest extends TestCase
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

    public function test_it_builds_a_smartbill_client_payload_from_collected_stripe_tax_ids(): void
    {
        $purchase = (new ReportPurchase())->forceFill([
            'id' => 1,
            'email' => 'office@acme.test',
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
                'name' => 'ACME Imobiliare SRL',
                'business_name' => 'ACME Imobiliare SRL',
                'tax_ids' => [
                    [
                        'type' => 'eu_vat',
                        'value' => 'RO12345678',
                    ],
                ],
            ],
        ]);
        $service = app(SmartBillService::class);
        $method = new \ReflectionMethod($service, 'buildClientPayload');

        $payload = $method->invoke($service, $purchase);

        $this->assertSame('ACME Imobiliare SRL', $payload['name']);
        $this->assertSame('RO12345678', $payload['vatCode']);
        $this->assertTrue($payload['isTaxPayer']);
        $this->assertSame('office@acme.test', $payload['email']);
        $this->assertSame('0711223344', $payload['phone']);
    }

    public function test_it_builds_a_stable_product_code_for_regular_reports(): void
    {
        $purchase = (new ReportPurchase())->forceFill([
            'id' => 7,
            'report_type' => 'buying_living',
            'locale' => 'ro',
            'currency' => 'ron',
            'paid_currency' => 'ron',
            'amount_total' => 11900,
        ]);
        $service = app(SmartBillService::class);
        $method = new \ReflectionMethod($service, 'buildProductPayload');

        $payload = $method->invoke($service, $purchase);

        $this->assertSame('REPORT-BUYING-LIVING', $payload['code']);
        $this->assertSame('Raport cumparare locuinta', $payload['name']);
    }

    public function test_it_builds_a_stable_product_code_for_billing_tests(): void
    {
        $purchase = (new ReportPurchase())->forceFill([
            'id' => 8,
            'report_type' => 'buying_living',
            'locale' => 'ro',
            'currency' => 'ron',
            'paid_currency' => 'ron',
            'amount_total' => 500,
            'report' => (object) ['is_test' => true],
        ]);
        $service = app(SmartBillService::class);
        $method = new \ReflectionMethod($service, 'buildProductPayload');

        $payload = $method->invoke($service, $purchase);

        $this->assertSame('TEST-STRIPE-SMARTBILL', $payload['code']);
        $this->assertSame('Test Stripe + SmartBill', $payload['name']);
    }

    public function test_it_marks_billing_test_invoices_as_drafts(): void
    {
        $purchase = (new ReportPurchase())->forceFill([
            'id' => 9,
            'report_id' => 62,
            'report_type' => 'buying_living',
            'locale' => 'ro',
            'currency' => 'ron',
            'paid_currency' => 'ron',
            'amount_total' => 500,
            'email' => 'mathias@example.test',
            'customer_email' => 'mathias@example.test',
            'customer_name' => 'Mathias Mihalcea',
            'customer_phone' => '+40774609510',
            'customer_address' => [
                'line1' => 'Strada Principala 45',
                'city' => 'Plopu',
                'state' => 'Prahova',
                'postal_code' => '107405',
                'country' => 'RO',
            ],
            'report' => (object) ['is_test' => true],
        ]);
        $service = app(SmartBillService::class);
        $method = new \ReflectionMethod($service, 'buildInvoicePayload');

        $payload = $method->invoke($service, $purchase);

        $this->assertTrue($payload['isDraft']);
        $this->assertSame('Billing test report #62 / Purchase #9 / Stripe payment intent n/a', $payload['observations']);
    }

    public function test_it_can_disable_draft_invoices_for_billing_tests_from_config(): void
    {
        config()->set('services.smartbill.invoice.test_draft', false);

        $purchase = (new ReportPurchase())->forceFill([
            'id' => 10,
            'report_id' => 63,
            'report_type' => 'buying_living',
            'locale' => 'ro',
            'currency' => 'ron',
            'paid_currency' => 'ron',
            'amount_total' => 500,
            'report' => (object) ['is_test' => true],
        ]);
        $service = app(SmartBillService::class);
        $method = new \ReflectionMethod($service, 'buildInvoicePayload');

        $payload = $method->invoke($service, $purchase);

        $this->assertFalse($payload['isDraft']);
    }

    public function test_it_builds_a_pdf_download_url_for_issued_invoices(): void
    {
        $service = app(SmartBillService::class);
        $method = new \ReflectionMethod($service, 'invoicePdfDownloadUrl');

        $downloadUrl = $method->invoke($service, 'GYC', '0023');

        $this->assertSame(
            'https://smartbill.test/SBORO/api/invoice/pdf?cif=RO12345678&seriesname=GYC&number=0023',
            $downloadUrl,
        );
    }

    public function test_it_does_not_build_a_pdf_download_url_without_invoice_number(): void
    {
        $service = app(SmartBillService::class);
        $method = new \ReflectionMethod($service, 'invoicePdfDownloadUrl');

        $downloadUrl = $method->invoke($service, 'GYC', null);

        $this->assertNull($downloadUrl);
    }

    public function test_it_accepts_billing_test_draft_responses_without_number(): void
    {
        $purchase = (new ReportPurchase())->forceFill([
            'report' => (object) ['is_test' => true],
        ]);
        $service = app(SmartBillService::class);
        $method = new \ReflectionMethod($service, 'acceptsDraftInvoiceResponse');

        $accepted = $method->invoke($service, $purchase, 'GYC', '');

        $this->assertTrue($accepted);
    }

    public function test_it_skips_payment_registration_for_billing_test_drafts(): void
    {
        $purchase = (new ReportPurchase())->forceFill([
            'report' => (object) ['is_test' => true],
        ]);
        $invoice = (new SmartBillInvoice())->forceFill([
            'status' => 'draft',
            'payment_status' => 'skipped',
            'invoice_series' => 'GYC',
        ]);
        $service = app(SmartBillService::class);
        $method = new \ReflectionMethod($service, 'shouldRegisterPayment');

        $shouldRegister = $method->invoke($service, $purchase, $invoice);

        $this->assertFalse($shouldRegister);
    }
}