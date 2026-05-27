<?php

namespace Tests\Unit;

use App\Models\Report;
use App\Models\ReportPurchase;
use App\Services\PaidReportFulfillmentService;
use App\Services\ReportPricingService;
use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Tests\TestCase;

class StripeCheckoutServiceTest extends TestCase
{
    public function test_it_uses_ron_for_ro_hosts_and_eur_for_com_hosts(): void
    {
        config()->set('services.stripe.currency', 'eur');
        config()->set('services.stripe.currencies', [
            'en' => 'eur',
            'ro' => 'ron',
        ]);

        $service = new StripeCheckoutService(
            app(PaidReportFulfillmentService::class),
            app(ReportPricingService::class),
        );
        $method = new \ReflectionMethod($service, 'currencyForLocale');

        $this->assertSame('ron', $method->invoke($service, 'ro'));
        $this->assertSame('eur', $method->invoke($service, 'en'));
    }

    public function test_it_resolves_checkout_locale_from_the_request_host(): void
    {
        $service = new StripeCheckoutService(
            app(PaidReportFulfillmentService::class),
            app(ReportPricingService::class),
        );
        $method = new \ReflectionMethod($service, 'checkoutLocale');
        $report = new Report(['locale' => 'en']);

        app()->instance('request', Request::create('https://customer.example.ro/report', 'GET'));
        $this->assertSame('ro', $method->invoke($service, $report));

        app()->instance('request', Request::create('https://customer.example.com/report', 'GET'));
        $this->assertSame('en', $method->invoke($service, $report));

        app()->forgetInstance('request');
    }

    public function test_it_uses_admin_return_urls_for_billing_test_reports(): void
    {
        $service = new StripeCheckoutService(
            app(PaidReportFulfillmentService::class),
            app(ReportPricingService::class),
        );

        $report = (new Report([
            'report_type' => 'buying_living',
            'locale' => 'ro',
            'email' => 'billing-test@example.com',
            'page_token' => 'billing-test-token',
            'is_test' => true,
        ]))->forceFill([
            'id' => 45,
        ]);
        $purchase = (new ReportPurchase())->forceFill([
            'id' => 67,
        ]);

        app()->instance('request', Request::create('https://admin.example.ro/admin/dashboard', 'GET'));

        $successUrl = new \ReflectionMethod($service, 'successUrl');
        $cancelUrl = new \ReflectionMethod($service, 'cancelUrl');

        $this->assertSame(
            'https://admin.example.ro/admin/billing-tests/45/checkout/success?session_id={CHECKOUT_SESSION_ID}&purchase=67',
            $successUrl->invoke($service, $report, $purchase),
        );
        $this->assertSame(
            'https://admin.example.ro/admin/billing-tests/45/checkout/cancel?purchase=67',
            $cancelUrl->invoke($service, $report, $purchase),
        );

        app()->forgetInstance('request');
    }

    public function test_it_uses_only_supported_billing_fields_for_checkout_sessions(): void
    {
        $service = new StripeCheckoutService(
            app(PaidReportFulfillmentService::class),
            app(ReportPricingService::class),
        );
        $method = new \ReflectionMethod($service, 'checkoutBillingCollectionPayload');

        $this->assertSame([
            'billing_address_collection' => 'required',
            'phone_number_collection' => [
                'enabled' => true,
            ],
        ], $method->invoke($service));
    }

}