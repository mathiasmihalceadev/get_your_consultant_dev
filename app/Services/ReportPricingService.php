<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Settings;
use Closure;
use Illuminate\Http\Request;
use RuntimeException;

class ReportPricingService
{
    public function __construct(
        private readonly ?Closure $settingResolver = null,
    ) {
    }

    private const DEFAULT_BASE_PRICES = [
        'rental_living' => '17.99',
        'buying_living' => '27.99',
    ];

    private const PRICE_SETTING_KEYS = [
        'rental_living' => 'pricing_rental_living_eur',
        'buying_living' => 'pricing_buying_living_eur',
    ];

    private const STRIPE_PRODUCT_SETTING_KEYS = [
        'rental_living' => 'stripe_product_rental_living',
        'buying_living' => 'stripe_product_buying_living',
    ];

    private const RATE_SETTING_KEY = 'pricing_exchange_rate_eur_ron';

    private const DEFAULT_EUR_RON_RATE = '5.00';

    private const PUBLIC_REPORT_TYPES = [
        'buying_living',
        'rental_living',
    ];

    public function pricingForCheckout(Report $report, ?Request $request = null): array
    {
        return $this->buildPricing(
            $report->report_type,
            $report->locale,
            $request,
            true,
        );
    }

    public function catalogForRequest(?string $fallbackLocale = null, ?Request $request = null): array
    {
        $catalog = [];

        foreach (self::PUBLIC_REPORT_TYPES as $reportType) {
            $catalog[$reportType] = $this->buildPricing(
                $reportType,
                $fallbackLocale,
                $request,
                false,
            );
        }

        return $catalog;
    }

    private function buildPricing(
        string $reportType,
        ?string $fallbackLocale,
        ?Request $request,
        bool $requireStripeProductId,
    ): array {
        $checkoutLocale = $this->checkoutLocale($request, $fallbackLocale);
        // Both public domains use the Romanian billing flow. Locale controls
        // presentation and report language, never checkout currency.
        $checkoutCurrency = 'ron';
        $baseAmount = $this->baseAmountEur($reportType);
        $baseAmountMinor = $this->decimalToScaledInteger($baseAmount, 2);
        $exchangeRate = $this->eurRonRate();
        $checkoutAmountMinor = $this->convertEurMinorToRonMinor($baseAmountMinor, $exchangeRate);

        return [
            'report_type' => $reportType,
            'checkout_locale' => $checkoutLocale,
            'base_currency' => 'eur',
            'base_amount' => $baseAmount,
            'base_amount_minor' => $baseAmountMinor,
            'checkout_currency' => $checkoutCurrency,
            'checkout_amount' => $this->minorToDecimalString($checkoutAmountMinor),
            'checkout_amount_minor' => $checkoutAmountMinor,
            'exchange_rate' => $exchangeRate,
            'stripe_product_id' => $requireStripeProductId
                ? $this->stripeProductId($reportType)
                : $this->configuredStripeProductId($reportType),
        ];
    }

    private function checkoutLocale(?Request $request, ?string $fallbackLocale): string
    {
        $locale = strtolower((string) ($request?->attributes->get('active_locale') ?: $fallbackLocale ?: app()->getLocale()));

        return in_array($locale, ['en', 'ro'], true) ? $locale : 'ro';
    }

    private function baseAmountEur(string $reportType): string
    {
        $settingKey = self::PRICE_SETTING_KEYS[$reportType] ?? null;
        $defaultPrice = self::DEFAULT_BASE_PRICES[$reportType] ?? null;

        if (!$settingKey || !$defaultPrice) {
            throw new RuntimeException("Pricing is not configured for report type [{$reportType}].");
        }

        $configuredValue = $this->setting($settingKey, $defaultPrice);

        return $this->normalizeDecimalValue((string) $configuredValue, $settingKey, 2);
    }

    private function eurRonRate(): string
    {
        $configuredValue = $this->setting(self::RATE_SETTING_KEY, self::DEFAULT_EUR_RON_RATE);

        return $this->normalizeDecimalValue((string) $configuredValue, self::RATE_SETTING_KEY, 6);
    }

    private function stripeProductId(string $reportType): string
    {
        $productId = $this->configuredStripeProductId($reportType);

        if ($productId === null) {
            throw new RuntimeException("Stripe product ID is not configured for report type [{$reportType}].");
        }

        return $productId;
    }

    private function configuredStripeProductId(string $reportType): ?string
    {
        $settingKey = self::STRIPE_PRODUCT_SETTING_KEYS[$reportType] ?? null;

        if (!$settingKey) {
            throw new RuntimeException("Stripe product mapping is not configured for report type [{$reportType}].");
        }

        $productId = trim((string) $this->setting(
            $settingKey,
            (string) config("services.stripe.products.{$reportType}", ''),
        ));

        return $productId !== '' ? $productId : null;
    }

    private function convertEurMinorToRonMinor(int $eurMinor, string $exchangeRate): int
    {
        $scaledRate = $this->decimalToScaledInteger($exchangeRate, 6);

        return (int) floor((($eurMinor * $scaledRate) + 500000) / 1000000);
    }

    private function decimalToScaledInteger(string $value, int $scale): int
    {
        $normalized = $this->normalizeDecimalValue($value, 'amount', $scale);
        $parts = explode('.', $normalized, 2);
        $whole = $parts[0];
        $fraction = str_pad($parts[1] ?? '', $scale, '0');

        return ((int) $whole * (10 ** $scale)) + (int) substr($fraction, 0, $scale);
    }

    private function minorToDecimalString(int $minor): string
    {
        return number_format($minor / 100, 2, '.', '');
    }

    private function normalizeDecimalValue(string $value, string $settingKey, int $maxScale): string
    {
        $normalized = str_replace(',', '.', trim($value));

        if (!preg_match('/^\d+(\.\d{1,' . $maxScale . '})?$/', $normalized)) {
            throw new RuntimeException("Setting [{$settingKey}] must be a positive decimal value.");
        }

        return $normalized;
    }

    private function setting(string $key, mixed $default = null): mixed
    {
        if ($this->settingResolver) {
            return ($this->settingResolver)($key, $default);
        }

        return Settings::get($key, $default);
    }
}
