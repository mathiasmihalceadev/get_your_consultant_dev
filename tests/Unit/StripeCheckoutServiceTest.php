<?php

namespace Tests\Unit;

use App\Models\Report;
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
}