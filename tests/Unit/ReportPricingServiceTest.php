<?php

namespace Tests\Unit;

use App\Models\Report;
use App\Services\ReportPricingService;
use Illuminate\Http\Request;
use Tests\TestCase;

class ReportPricingServiceTest extends TestCase
{
    public function test_it_builds_ron_checkout_pricing_from_the_eur_base_price_on_ro_hosts(): void
    {
        $service = new ReportPricingService(
            fn (string $key, mixed $default = null) => [
                'pricing_rental_living_eur' => '17.99',
                'pricing_exchange_rate_eur_ron' => '5.05',
                'stripe_product_rental_living' => 'prod_rental_living',
            ][$key] ?? $default,
        );
        $report = Report::make([
            'report_type' => 'rental_living',
            'locale' => 'ro',
        ]);

        $pricing = $service->pricingForCheckout(
            $report,
            Request::create('https://customer.example.ro/get-report', 'GET'),
        );

        $this->assertSame('ro', $pricing['checkout_locale']);
        $this->assertSame('ron', $pricing['checkout_currency']);
        $this->assertSame('eur', $pricing['base_currency']);
        $this->assertSame('17.99', $pricing['base_amount']);
        $this->assertSame(1799, $pricing['base_amount_minor']);
        $this->assertSame('90.85', $pricing['checkout_amount']);
        $this->assertSame(9085, $pricing['checkout_amount_minor']);
        $this->assertSame('5.05', $pricing['exchange_rate']);
        $this->assertSame('prod_rental_living', $pricing['stripe_product_id']);
    }

    public function test_catalog_pricing_keeps_eur_amounts_for_com_hosts_without_requiring_product_ids(): void
    {
        $service = new ReportPricingService(
            fn (string $key, mixed $default = null) => [
                'pricing_rental_living_eur' => '17.99',
                'pricing_buying_living_eur' => '27.99',
                'pricing_exchange_rate_eur_ron' => '5.05',
            ][$key] ?? $default,
        );
        $catalog = $service->catalogForRequest(
            'en',
            Request::create('https://customer.example.com/get-report', 'GET'),
        );

        $this->assertSame('eur', $catalog['rental_living']['checkout_currency']);
        $this->assertSame(1799, $catalog['rental_living']['checkout_amount_minor']);
        $this->assertSame('eur', $catalog['buying_living']['checkout_currency']);
        $this->assertSame(2799, $catalog['buying_living']['checkout_amount_minor']);
        $this->assertNull($catalog['buying_living']['stripe_product_id']);
    }
}