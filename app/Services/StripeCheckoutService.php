<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportPurchase;
use App\Support\LocalizedUrl;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeCheckoutService
{
    private ?StripeClient $client = null;

    public function __construct(
        private readonly PaidReportFulfillmentService $paidReportFulfillment,
        private readonly ReportPricingService $reportPricing,
    ) {
    }

    public function createCheckoutSession(Report $report): string
    {
        if (!$report->email || !$report->page_token) {
            throw new RuntimeException('Report email and page token are required before creating a checkout session.');
        }

        $pricing = $this->reportPricing->pricingForCheckout($report, request());

        $metadata = array_filter([
            'report_id' => (string) $report->id,
            'page_token' => $report->page_token,
            'report_type' => $report->report_type,
            'locale' => $report->locale,
            'checkout_locale' => $pricing['checkout_locale'],
            'checkout_currency' => $pricing['checkout_currency'],
            'base_currency' => $pricing['base_currency'],
            'base_amount_minor' => (string) $pricing['base_amount_minor'],
            'checkout_amount_minor' => (string) $pricing['checkout_amount_minor'],
            'exchange_rate' => $pricing['exchange_rate'] !== null ? (string) $pricing['exchange_rate'] : null,
            'stripe_product_id' => $pricing['stripe_product_id'],
            'affiliate_tag_id' => $report->affiliate_tag_id ? (string) $report->affiliate_tag_id : null,
            'affiliate_ref' => $report->affiliate_ref,
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        $purchase = ReportPurchase::create([
            'report_id' => $report->id,
            'report_type' => $report->report_type,
            'locale' => $report->locale,
            'email' => $report->email,
            'status' => 'checkout_created',
            'currency' => $pricing['checkout_currency'],
            'base_currency' => $pricing['base_currency'],
            'base_amount_minor' => $pricing['base_amount_minor'],
            'checkout_amount_minor' => $pricing['checkout_amount_minor'],
            'exchange_rate' => $pricing['exchange_rate'],
            'stripe_product_id' => $pricing['stripe_product_id'],
            'affiliate_tag_id' => $report->affiliate_tag_id,
            'affiliate_ref' => $report->affiliate_ref,
            'metadata' => $metadata,
            'checkout_started_at' => now(),
        ]);

        $payload = [
            'mode' => 'payment',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $pricing['checkout_currency'],
                        'product' => $pricing['stripe_product_id'],
                        'unit_amount' => $pricing['checkout_amount_minor'],
                    ],
                    'quantity' => 1,
                ],
            ],
            'customer_email' => $report->email,
            'customer_creation' => 'always',
            ...$this->checkoutBillingCollectionPayload(),
            'metadata' => [
                'report_id' => (string) $report->id,
                'purchase_id' => (string) $purchase->id,
                ...$metadata,
            ],
            'success_url' => $this->successUrl($report, $purchase),
            'cancel_url' => $this->cancelUrl($report, $purchase),
        ];

        $this->logInfo('Creating Stripe checkout session', [
            'report_id' => $report->id,
            'purchase_id' => $purchase->id,
            'payload' => $payload,
        ]);

        try {
            $session = $this->client()->checkout->sessions->create($payload);
        } catch (ApiErrorException $e) {
            $purchase->update([
                'status' => 'failed',
                'failed_at' => now(),
            ]);

            $this->logError('Stripe checkout session creation failed', [
                'report_id' => $report->id,
                'purchase_id' => $purchase->id,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Unable to create Stripe checkout session.', previous: $e);
        }

        $purchase->update([
            'status' => 'checkout_open',
            'stripe_checkout_session_id' => $session->id,
            'amount_subtotal' => $session->amount_subtotal,
            'amount_total' => $session->amount_total,
            'checkout_session_payload' => $session->toArray(),
            'customer_email' => $session->customer_email ?: $report->email,
        ]);

        $report->update([
            'status' => 'awaiting_payment',
            'error_message' => null,
        ]);

        $this->logInfo('Stripe checkout session created', [
            'report_id' => $report->id,
            'purchase_id' => $purchase->id,
            'checkout_session_id' => $session->id,
            'checkout_url' => $session->url,
        ]);

        return (string) $session->url;
    }

    public function createBillingTestCheckoutSession(Report $report, bool $sendTestInvoiceEmail = false): string
    {
        if (!$report->email || !$report->page_token) {
            throw new RuntimeException('Billing test report email and page token are required before creating a checkout session.');
        }

        if (!$report->is_test) {
            throw new RuntimeException('Billing test checkout can only be created for reports marked as tests.');
        }

        $currency = $this->billingTestCurrency($report->locale);
        $amountMinor = $this->billingTestAmountMinor($currency);
        $metadata = array_filter([
            'report_id' => (string) $report->id,
            'page_token' => $report->page_token,
            'report_type' => $report->report_type,
            'locale' => $report->locale,
            'checkout_locale' => $report->locale === 'ro' ? 'ro' : 'en',
            'checkout_currency' => $currency,
            'base_currency' => $currency,
            'base_amount_minor' => (string) $amountMinor,
            'checkout_amount_minor' => (string) $amountMinor,
            'billing_test' => '1',
            'send_test_invoice_email' => $sendTestInvoiceEmail ? '1' : '0',
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        $purchase = ReportPurchase::create([
            'report_id' => $report->id,
            'report_type' => $report->report_type,
            'locale' => $report->locale,
            'email' => $report->email,
            'status' => 'checkout_created',
            'currency' => $currency,
            'base_currency' => $currency,
            'base_amount_minor' => $amountMinor,
            'checkout_amount_minor' => $amountMinor,
            'metadata' => $metadata,
            'checkout_started_at' => now(),
        ]);

        $payload = [
            'mode' => 'payment',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $this->billingTestProductName($report),
                            'description' => $this->billingTestProductDescription($report),
                        ],
                        'unit_amount' => $amountMinor,
                    ],
                    'quantity' => 1,
                ],
            ],
            'customer_email' => $report->email,
            'customer_creation' => 'always',
            ...$this->checkoutBillingCollectionPayload(),
            'metadata' => [
                'report_id' => (string) $report->id,
                'purchase_id' => (string) $purchase->id,
                ...$metadata,
            ],
            'success_url' => $this->successUrl($report, $purchase),
            'cancel_url' => $this->cancelUrl($report, $purchase),
        ];

        $this->logInfo('Creating Stripe billing test checkout session', [
            'report_id' => $report->id,
            'purchase_id' => $purchase->id,
            'payload' => $payload,
        ]);

        try {
            $session = $this->client()->checkout->sessions->create($payload);
        } catch (ApiErrorException $e) {
            $purchase->update([
                'status' => 'failed',
                'failed_at' => now(),
            ]);

            $report->update([
                'status' => 'error',
                'error_message' => 'Unable to create the Stripe billing test checkout session.',
            ]);

            $this->logError('Stripe billing test checkout session creation failed', [
                'report_id' => $report->id,
                'purchase_id' => $purchase->id,
                'error' => $e->getMessage(),
            ]);

            throw new RuntimeException('Unable to create Stripe billing test checkout session.', previous: $e);
        }

        $purchase->update([
            'status' => 'checkout_open',
            'stripe_checkout_session_id' => $session->id,
            'amount_subtotal' => $session->amount_subtotal,
            'amount_total' => $session->amount_total,
            'checkout_session_payload' => $session->toArray(),
            'customer_email' => $session->customer_email ?: $report->email,
        ]);

        $report->update([
            'status' => 'awaiting_payment',
            'error_message' => null,
        ]);

        $this->logInfo('Stripe billing test checkout session created', [
            'report_id' => $report->id,
            'purchase_id' => $purchase->id,
            'checkout_session_id' => $session->id,
            'checkout_url' => $session->url,
        ]);

        return (string) $session->url;
    }

    public function markCheckoutSuccessReturn(Report $report, ?int $purchaseId, ?string $sessionId): void
    {
        $purchase = $this->findPurchase($report, $purchaseId, $sessionId);
        $sessionPaymentStatus = null;

        if ($sessionId) {
            try {
                $session = $this->retrieveCheckoutSession($sessionId);
                $sessionPaymentStatus = $session->payment_status;

                if ($purchase) {
                    $customerPayload = is_object($session->customer)
                        ? $this->normalizeStripeValue($session->customer)
                        : null;
                    $customerDetails = $this->mergeStripeCustomerDetails(
                        $this->normalizeStripeValue($session->customer_details),
                        $customerPayload,
                    );

                    $purchase->update([
                        'status' => $session->payment_status === 'paid'
                            ? 'payment_processing'
                            : $purchase->status,
                        'stripe_checkout_session_id' => $session->id,
                        'amount_subtotal' => $session->amount_subtotal,
                        'amount_total' => $session->amount_total,
                        'paid_currency' => strtolower((string) ($session->currency ?: $purchase->paid_currency ?: $purchase->currency)),
                        'customer_email' => $customerDetails['email'] ?? $session->customer_email ?? $purchase->email,
                        'customer_name' => $this->resolveStripeCustomerName($customerDetails) ?? $purchase->customer_name,
                        'customer_phone' => $customerDetails['phone'] ?? $purchase->customer_phone,
                        'customer_address' => is_array($customerDetails['address'] ?? null)
                            ? $customerDetails['address']
                            : $purchase->customer_address,
                        'customer_details' => $customerDetails ?? $purchase->customer_details,
                        'checkout_session_payload' => $session->toArray(),
                    ]);
                }

                if (
                    $session->payment_status === 'paid'
                    && !in_array($report->status, ['pending', 'to_be_sent', 'sent'], true)
                ) {
                    $report->update([
                        'status' => 'payment_processing',
                        'error_message' => null,
                    ]);
                }
            } catch (\Throwable $e) {
                $this->logError('Unable to verify Stripe checkout session on success redirect', [
                    'report_id' => $report->id,
                    'purchase_id' => $purchase?->id,
                    'checkout_session_id' => $sessionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->logInfo('Stripe checkout success redirect received', [
            'report_id' => $report->id,
            'purchase_id' => $purchase?->id,
            'checkout_session_id' => $sessionId,
            'report_status' => $report->status,
            'purchase_status' => $purchase?->status,
            'session_payment_status' => $sessionPaymentStatus,
        ]);
    }

    public function markCheckoutCanceled(Report $report, ?int $purchaseId): void
    {
        $purchase = $this->findPurchase($report, $purchaseId, null);

        if ($purchase && !in_array($purchase->status, ['paid', 'canceled'], true)) {
            $purchase->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }

        if (!in_array($report->status, ['pending', 'to_be_sent', 'sent'], true)) {
            $report->update([
                'status' => 'payment_cancelled',
                'error_message' => null,
            ]);
        }

        $this->logInfo('Stripe checkout cancel redirect received', [
            'report_id' => $report->id,
            'purchase_id' => $purchase?->id,
            'status' => $report->status,
        ]);
    }

    public function handleWebhook(string $payload, ?string $signatureHeader): array
    {
        $event = Webhook::constructEvent(
            $payload,
            (string) $signatureHeader,
            $this->webhookSecret(),
        );

        $eventPayload = $event->toArray();

        $this->logInfo('Stripe webhook received', [
            'event_id' => $event->id,
            'type' => $event->type,
            'payload' => $eventPayload,
        ]);

        return match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted(
                (string) $event->data->object->id,
                $event->id,
                $event->type,
                $eventPayload,
            ),
            'checkout.session.async_payment_succeeded' => $this->handlePaidCheckoutSession(
                (string) $event->data->object->id,
                $event->id,
                $event->type,
                $eventPayload,
            ),
            'checkout.session.async_payment_failed' => $this->handleFailedCheckoutSession(
                (string) $event->data->object->id,
                $event->id,
                $event->type,
                $eventPayload,
                'failed',
            ),
            'checkout.session.expired' => $this->handleFailedCheckoutSession(
                (string) $event->data->object->id,
                $event->id,
                $event->type,
                $eventPayload,
                'expired',
            ),
            default => $this->handleUnhandledEvent($event->id, $event->type, $eventPayload),
        };
    }

    private function handleCheckoutSessionCompleted(
        string $sessionId,
        string $eventId,
        string $eventType,
        array $eventPayload,
    ): array {
        $session = $this->retrieveCheckoutSession($sessionId);

        if ($session->payment_status === 'paid') {
            return $this->handlePaidCheckoutSession($sessionId, $eventId, $eventType, $eventPayload, $session);
        }

        $purchase = $this->syncPurchaseFromSession(
            $session,
            'payment_processing',
            $eventId,
            $eventType,
            $eventPayload,
        );

        if ($purchase?->status === 'failed') {
            return [
                'handled' => true,
                'event_id' => $eventId,
                'type' => $eventType,
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
            ];
        }

        $report = $purchase?->report;

        if ($report && !in_array($report->status, ['pending', 'to_be_sent', 'sent'], true)) {
            $report->update([
                'status' => 'payment_processing',
                'error_message' => null,
            ]);
        }

        $this->logInfo('Stripe checkout session completed without immediate payment confirmation', [
            'event_id' => $eventId,
            'event_type' => $eventType,
            'checkout_session_id' => $sessionId,
            'purchase_id' => $purchase?->id,
            'report_id' => $report?->id,
            'payment_status' => $session->payment_status,
        ]);

        return [
            'handled' => true,
            'event_id' => $eventId,
            'type' => $eventType,
            'purchase_id' => $purchase?->id,
        ];
    }

    private function handlePaidCheckoutSession(
        string $sessionId,
        string $eventId,
        string $eventType,
        array $eventPayload,
        ?Session $session = null,
    ): array {
        $session ??= $this->retrieveCheckoutSession($sessionId);

        $purchase = $this->syncPurchaseFromSession(
            $session,
            'paid',
            $eventId,
            $eventType,
            $eventPayload,
        );

        if (!$purchase || !$purchase->report) {
            throw new RuntimeException('Unable to resolve a report purchase for the paid Stripe checkout session.');
        }

        if ($purchase->status === 'failed') {
            return [
                'handled' => true,
                'event_id' => $eventId,
                'type' => $eventType,
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
            ];
        }

        $purchase->report->update([
            'status' => 'payment_processing',
            'error_message' => null,
        ]);

        $this->smartBillPurchaseSyncService()->syncPaidPurchase($purchase);

        if ($purchase->report->is_test) {
            $this->logInfo('Stripe payment confirmed for admin billing test', [
                'event_id' => $eventId,
                'event_type' => $eventType,
                'checkout_session_id' => $sessionId,
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
            ]);

            return [
                'handled' => true,
                'event_id' => $eventId,
                'type' => $eventType,
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
            ];
        }

        $this->paidReportFulfillment->fulfill($purchase->report->fresh());

        $this->logInfo('Stripe payment confirmed', [
            'event_id' => $eventId,
            'event_type' => $eventType,
            'checkout_session_id' => $sessionId,
            'purchase_id' => $purchase->id,
            'report_id' => $purchase->report_id,
        ]);

        return [
            'handled' => true,
            'event_id' => $eventId,
            'type' => $eventType,
            'purchase_id' => $purchase->id,
            'report_id' => $purchase->report_id,
        ];
    }

    private function handleFailedCheckoutSession(
        string $sessionId,
        string $eventId,
        string $eventType,
        array $eventPayload,
        string $purchaseStatus,
    ): array {
        $session = $this->retrieveCheckoutSession($sessionId);

        $purchase = $this->syncPurchaseFromSession(
            $session,
            $purchaseStatus,
            $eventId,
            $eventType,
            $eventPayload,
        );

        $report = $purchase?->report ?? $this->resolveReportFromSession($session);

        if ($report && !in_array($report->status, ['pending', 'to_be_sent', 'sent'], true)) {
            $report->update([
                'status' => 'payment_failed',
                'error_message' => null,
            ]);
        }

        $this->logInfo('Stripe checkout session failed or expired', [
            'event_id' => $eventId,
            'event_type' => $eventType,
            'checkout_session_id' => $sessionId,
            'purchase_id' => $purchase?->id,
            'report_id' => $report?->id,
            'purchase_status' => $purchaseStatus,
        ]);

        return [
            'handled' => true,
            'event_id' => $eventId,
            'type' => $eventType,
            'purchase_id' => $purchase?->id,
        ];
    }

    private function handleUnhandledEvent(string $eventId, string $eventType, array $eventPayload): array
    {
        $this->logInfo('Stripe webhook event ignored', [
            'event_id' => $eventId,
            'event_type' => $eventType,
            'payload' => $eventPayload,
        ]);

        return [
            'handled' => false,
            'event_id' => $eventId,
            'type' => $eventType,
        ];
    }

    private function retrieveCheckoutSession(string $sessionId): Session
    {
        return $this->client()->checkout->sessions->retrieve($sessionId, [
            'expand' => ['payment_intent', 'customer', 'customer.tax_ids'],
        ]);
    }

    private function syncPurchaseFromSession(
        Session $session,
        string $status,
        string $eventId,
        string $eventType,
        array $eventPayload,
    ): ?ReportPurchase {
        $purchase = $this->resolvePurchaseFromSession($session);

        if (!$purchase) {
            $this->logError('Unable to resolve purchase from Stripe checkout session', [
                'checkout_session_id' => $session->id,
                'status' => $status,
                'event_id' => $eventId,
                'event_type' => $eventType,
            ]);

            return null;
        }

        if ($failedPurchase = $this->markPricingMismatchIfNeeded($purchase, $session, $eventId, $eventType, $eventPayload)) {
            return $failedPurchase;
        }

        $metadata = [
            ...(is_array($purchase->metadata) ? $purchase->metadata : []),
            ...($this->normalizeStripeValue($session->metadata) ?? []),
        ];
        $customerPayload = is_object($session->customer)
            ? $this->normalizeStripeValue($session->customer)
            : null;
        $customerDetails = $this->mergeStripeCustomerDetails(
            $this->normalizeStripeValue($session->customer_details),
            $customerPayload,
        );
        $paymentIntentPayload = is_object($session->payment_intent)
            ? $this->normalizeStripeValue($session->payment_intent)
            : null;

        $purchase->fill([
            'email' => $purchase->email ?: $purchase->report?->email,
            'status' => $status,
            'amount_subtotal' => $session->amount_subtotal,
            'amount_total' => $session->amount_total,
            'paid_currency' => strtolower((string) ($session->currency ?: $purchase->paid_currency ?: $purchase->currency)),
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id' => is_string($session->payment_intent)
                ? $session->payment_intent
                : $session->payment_intent?->id,
            'stripe_customer_id' => is_string($session->customer)
                ? $session->customer
                : $session->customer?->id,
            'stripe_price_id' => (string) ($metadata['price_id'] ?? $purchase->stripe_price_id ?? ''),
            'stripe_product_id' => (string) ($metadata['stripe_product_id'] ?? $purchase->stripe_product_id ?? ''),
            'customer_email' => $customerDetails['email'] ?? $session->customer_email ?? $purchase->email,
            'customer_name' => $this->resolveStripeCustomerName($customerDetails),
            'customer_phone' => $customerDetails['phone'] ?? null,
            'customer_address' => is_array($customerDetails['address'] ?? null) ? $customerDetails['address'] : null,
            'customer_details' => $customerDetails,
            'checkout_session_payload' => $session->toArray(),
            'payment_intent_payload' => $paymentIntentPayload,
            'latest_webhook_event_id' => $eventId,
            'latest_webhook_event_type' => $eventType,
            'latest_webhook_payload' => $eventPayload,
            'affiliate_tag_id' => $purchase->affiliate_tag_id
                ?: (isset($metadata['affiliate_tag_id']) ? (int) $metadata['affiliate_tag_id'] : null)
                ?: $purchase->report?->affiliate_tag_id,
            'affiliate_ref' => $purchase->affiliate_ref
                ?: ($metadata['affiliate_ref'] ?? null)
                ?: $purchase->report?->affiliate_ref,
            'metadata' => $metadata,
            'checkout_started_at' => $purchase->checkout_started_at ?: $this->stripeTimestamp($session->created) ?: now(),
            'paid_at' => $status === 'paid' ? ($purchase->paid_at ?: now()) : $purchase->paid_at,
            'failed_at' => in_array($status, ['failed', 'expired'], true) ? ($purchase->failed_at ?: now()) : $purchase->failed_at,
            'canceled_at' => $status === 'canceled' ? ($purchase->canceled_at ?: now()) : $purchase->canceled_at,
        ]);

        $purchase->save();

        return $purchase->fresh('report');
    }

    private function resolvePurchaseFromSession(Session $session): ?ReportPurchase
    {
        $metadata = $this->normalizeStripeValue($session->metadata) ?? [];
        $purchaseId = isset($metadata['purchase_id']) ? (int) $metadata['purchase_id'] : null;

        if ($purchaseId) {
            $purchase = ReportPurchase::with('report')->find($purchaseId);

            if ($purchase) {
                return $purchase;
            }
        }

        $purchase = ReportPurchase::with('report')
            ->where('stripe_checkout_session_id', $session->id)
            ->first();

        if ($purchase) {
            return $purchase;
        }

        $report = $this->resolveReportFromSession($session);

        if (!$report) {
            return null;
        }

        return ReportPurchase::create([
            'report_id' => $report->id,
            'report_type' => $report->report_type,
            'locale' => $report->locale,
            'email' => $report->email,
            'status' => 'checkout_open',
            'currency' => strtolower((string) (($metadata['checkout_currency'] ?? $metadata['currency'] ?? $session->currency) ?: $this->currency())),
            'paid_currency' => $session->currency ? strtolower((string) $session->currency) : null,
            'base_currency' => strtolower((string) ($metadata['base_currency'] ?? 'eur')),
            'base_amount_minor' => isset($metadata['base_amount_minor']) ? (int) $metadata['base_amount_minor'] : null,
            'checkout_amount_minor' => isset($metadata['checkout_amount_minor'])
                ? (int) $metadata['checkout_amount_minor']
                : $session->amount_total,
            'exchange_rate' => isset($metadata['exchange_rate']) && $metadata['exchange_rate'] !== ''
                ? (string) $metadata['exchange_rate']
                : null,
            'stripe_checkout_session_id' => $session->id,
            'stripe_price_id' => $metadata['price_id'] ?? null,
            'stripe_product_id' => $metadata['stripe_product_id'] ?? null,
            'affiliate_tag_id' => isset($metadata['affiliate_tag_id'])
                ? (int) $metadata['affiliate_tag_id']
                : $report->affiliate_tag_id,
            'affiliate_ref' => $metadata['affiliate_ref'] ?? $report->affiliate_ref,
            'metadata' => $metadata,
            'checkout_started_at' => $this->stripeTimestamp($session->created) ?: now(),
        ])->fresh('report');
    }

    private function markPricingMismatchIfNeeded(
        ReportPurchase $purchase,
        Session $session,
        string $eventId,
        string $eventType,
        array $eventPayload,
    ): ?ReportPurchase {
        $expectedCurrency = strtolower((string) $purchase->currency);
        $actualCurrency = strtolower((string) ($session->currency ?: ''));
        $expectedAmount = $purchase->checkout_amount_minor;
        $actualAmount = $session->amount_total;
        $mismatch = [];

        if ($expectedCurrency !== '' && $actualCurrency !== '' && $expectedCurrency !== $actualCurrency) {
            $mismatch['currency'] = [
                'expected' => $expectedCurrency,
                'actual' => $actualCurrency,
            ];
        }

        if ($expectedAmount !== null && $actualAmount !== null && (int) $expectedAmount !== (int) $actualAmount) {
            $mismatch['amount_total'] = [
                'expected' => (int) $expectedAmount,
                'actual' => (int) $actualAmount,
            ];
        }

        if ($mismatch === []) {
            return null;
        }

        $metadata = $purchase->metadata ?? [];
        $metadata['pricing_mismatch'] = $mismatch;

        $purchase->fill([
            'status' => 'failed',
            'amount_subtotal' => $session->amount_subtotal,
            'amount_total' => $session->amount_total,
            'paid_currency' => $actualCurrency !== '' ? $actualCurrency : $purchase->paid_currency,
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id' => is_string($session->payment_intent)
                ? $session->payment_intent
                : $session->payment_intent?->id,
            'stripe_customer_id' => is_string($session->customer)
                ? $session->customer
                : $session->customer?->id,
            'checkout_session_payload' => $session->toArray(),
            'payment_intent_payload' => is_object($session->payment_intent)
                ? $this->normalizeStripeValue($session->payment_intent)
                : null,
            'latest_webhook_event_id' => $eventId,
            'latest_webhook_event_type' => $eventType,
            'latest_webhook_payload' => $eventPayload,
            'metadata' => $metadata,
            'failed_at' => $purchase->failed_at ?: now(),
        ]);

        $purchase->save();

        if ($purchase->report && !in_array($purchase->report->status, ['pending', 'to_be_sent', 'sent'], true)) {
            $purchase->report->update([
                'status' => 'payment_failed',
                'error_message' => null,
            ]);
        }

        $this->logError('Stripe payment did not match the expected checkout pricing', [
            'purchase_id' => $purchase->id,
            'report_id' => $purchase->report_id,
            'checkout_session_id' => $session->id,
            'mismatch' => $mismatch,
        ]);

        return $purchase->fresh('report');
    }

    private function resolveReportFromSession(Session $session): ?Report
    {
        $metadata = $this->normalizeStripeValue($session->metadata) ?? [];
        $reportId = isset($metadata['report_id']) ? (int) $metadata['report_id'] : null;

        if ($reportId) {
            $report = Report::find($reportId);

            if ($report) {
                return $report;
            }
        }

        $pageToken = $metadata['page_token'] ?? null;

        if (!$pageToken) {
            return null;
        }

        return Report::where('page_token', $pageToken)->first();
    }

    private function findPurchase(Report $report, ?int $purchaseId, ?string $sessionId): ?ReportPurchase
    {
        if ($purchaseId) {
            $purchase = $report->purchases()->whereKey($purchaseId)->first();

            if ($purchase) {
                return $purchase;
            }
        }

        if ($sessionId) {
            $purchase = $report->purchases()
                ->where('stripe_checkout_session_id', $sessionId)
                ->first();

            if ($purchase) {
                return $purchase;
            }
        }

        return $report->purchases()->latest('id')->first();
    }

    private function successUrl(Report $report, ReportPurchase $purchase): string
    {
        if ($report->is_test) {
            $path = route('admin.billing-tests.success', ['id' => $report->id], false);

            return $this->checkoutBaseUrl($report)
                . $path
                . '?session_id={CHECKOUT_SESSION_ID}&purchase='
                . $purchase->id;
        }

        $path = route('checkout.success', ['pageToken' => $report->page_token], false);

        return $this->checkoutBaseUrl($report)
            . $path
            . '?session_id={CHECKOUT_SESSION_ID}&purchase='
            . $purchase->id;
    }

    private function cancelUrl(Report $report, ReportPurchase $purchase): string
    {
        if ($report->is_test) {
            $path = route('admin.billing-tests.cancel', ['id' => $report->id], false);

            return $this->checkoutBaseUrl($report)
                . $path
                . '?purchase='
                . $purchase->id;
        }

        $path = route('checkout.cancel', ['pageToken' => $report->page_token], false);

        return $this->checkoutBaseUrl($report)
            . $path
            . '?purchase='
            . $purchase->id;
    }

    private function checkoutBaseUrl(Report $report): string
    {
        $request = request();

        if ($request) {
            $schemeAndHost = $request->getSchemeAndHttpHost();

            if ($schemeAndHost !== '') {
                return rtrim($schemeAndHost, '/');
            }
        }

        return rtrim(
            (string) config("locales.domain_urls.{$report->locale}", config('app.url')),
            '/',
        );
    }

    private function checkoutLocale(Report $report): string
    {
        $request = request();

        if ($request) {
            $host = LocalizedUrl::requestHost($request);

            if (str_ends_with($host, '.ro')) {
                return 'ro';
            }

            if (str_ends_with($host, '.com')) {
                return 'en';
            }

            return LocalizedUrl::localeForHost($host);
        }

        $locale = strtolower((string) ($report->locale ?: LocalizedUrl::defaultLocale()));

        return in_array($locale, LocalizedUrl::supportedLocales(), true)
            ? $locale
            : LocalizedUrl::defaultLocale();
    }

    private function currencyForLocale(string $locale): string
    {
        $currency = strtolower((string) config("services.stripe.currencies.{$locale}", ''));

        if ($currency !== '') {
            return $currency;
        }

        return $locale === 'ro'
            ? 'ron'
            : $this->currency();
    }

    private function resolvePriceId(string $reportType): string
    {
        $priceId = (string) config("services.stripe.prices.{$reportType}");

        if ($priceId === '') {
            throw new RuntimeException("Stripe price ID is not configured for report type [{$reportType}].");
        }

        return $priceId;
    }

    private function currency(): string
    {
        return strtolower((string) config('services.stripe.currency', 'eur'));
    }

    private function checkoutBillingCollectionPayload(): array
    {
        return [
            'billing_address_collection' => 'required',
            'phone_number_collection' => [
                'enabled' => true,
            ],
        ];
    }

    private function billingTestCurrency(?string $locale): string
    {
        return strtolower($locale) === 'ro' ? 'ron' : 'eur';
    }

    private function billingTestAmountMinor(string $currency): int
    {
        return $currency === 'ron' ? 500 : 100;
    }

    private function billingTestProductName(Report $report): string
    {
        return match ($report->locale) {
            'ro' => 'Test Stripe + SmartBill',
            default => 'Stripe + SmartBill test',
        };
    }

    private function billingTestProductDescription(Report $report): string
    {
        $reportType = Str::replace('_', ' ', $report->report_type);

        return $report->locale === 'ro'
            ? 'Flux de test pentru plata si facturare. Nu se genereaza raportul final. Tip: ' . $reportType
            : 'Billing test flow for payment and invoicing. No final report will be generated. Type: ' . $reportType;
    }

    private function webhookSecret(): string
    {
        $secret = (string) config('services.stripe.webhook_secret');

        if ($secret === '') {
            throw new RuntimeException('Stripe webhook secret is not configured.');
        }

        return $secret;
    }

    private function client(): StripeClient
    {
        if ($this->client) {
            return $this->client;
        }

        $secretKey = (string) config('services.stripe.secret_key');

        if ($secretKey === '') {
            throw new RuntimeException('Stripe secret key is not configured.');
        }

        $this->client = new StripeClient($secretKey);

        return $this->client;
    }

    private function smartBillPurchaseSyncService(): SmartBillPurchaseSyncService
    {
        $service = app(SmartBillPurchaseSyncService::class);

        if (!$service instanceof SmartBillPurchaseSyncService) {
            throw new RuntimeException('Unable to resolve SmartBillPurchaseSyncService from the container.');
        }

        return $service;
    }

    private function normalizeStripeValue(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        $decoded = json_decode(json_encode($value), true);

        return is_array($decoded) ? $decoded : null;
    }

    private function mergeStripeCustomerDetails(?array $customerDetails, ?array $customerPayload): ?array
    {
        if ($customerDetails === null && $customerPayload === null) {
            return null;
        }

        $address = is_array($customerDetails['address'] ?? null)
            ? $customerDetails['address']
            : (is_array($customerPayload['address'] ?? null) ? $customerPayload['address'] : null);
        $taxIds = $this->extractStripeTaxIds($customerDetails, $customerPayload);

        $merged = array_filter([
            ...($customerDetails ?? []),
            'name' => $customerDetails['name']
                ?? $customerPayload['business_name']
                ?? $customerPayload['name']
                ?? $customerPayload['individual_name']
                ?? null,
            'email' => $customerDetails['email'] ?? $customerPayload['email'] ?? null,
            'phone' => $customerDetails['phone'] ?? $customerPayload['phone'] ?? null,
            'address' => $address,
            'business_name' => $customerPayload['business_name'] ?? $customerDetails['business_name'] ?? null,
            'individual_name' => $customerPayload['individual_name'] ?? $customerDetails['individual_name'] ?? null,
            'tax_exempt' => $customerPayload['tax_exempt'] ?? $customerDetails['tax_exempt'] ?? null,
            'tax_ids' => $taxIds,
        ], fn (mixed $value): bool => $this->hasStripeValue($value));

        return $merged === [] ? null : $merged;
    }

    private function extractStripeTaxIds(?array $customerDetails, ?array $customerPayload): array
    {
        $sources = [
            $customerDetails['tax_ids'] ?? [],
            $customerPayload['tax_ids']['data'] ?? [],
            $customerPayload['tax_ids'] ?? [],
        ];

        $taxIds = [];

        foreach ($sources as $source) {
            if (!is_array($source)) {
                continue;
            }

            foreach ($source as $taxId) {
                if (!is_array($taxId)) {
                    continue;
                }

                $value = trim((string) ($taxId['value'] ?? ''));

                if ($value === '') {
                    continue;
                }

                $signature = strtolower((string) ($taxId['type'] ?? '')) . '|' . strtolower($value);

                $taxIds[$signature] = array_filter([
                    'type' => $taxId['type'] ?? null,
                    'value' => $value,
                ], fn (mixed $item): bool => $item !== null && $item !== '');
            }
        }

        return array_values($taxIds);
    }

    private function resolveStripeCustomerName(?array $customerDetails): ?string
    {
        $name = trim((string) (
            $customerDetails['business_name']
            ?? $customerDetails['name']
            ?? $customerDetails['individual_name']
            ?? ''
        ));

        return $name !== '' ? $name : null;
    }

    private function hasStripeValue(mixed $value): bool
    {
        if (is_array($value)) {
            return $value !== [];
        }

        return $value !== null && $value !== '';
    }

    private function stripeTimestamp(?int $timestamp): ?Carbon
    {
        if (!$timestamp) {
            return null;
        }

        return Carbon::createFromTimestamp($timestamp);
    }

    private function logInfo(string $message, array $context = []): void
    {
        Log::channel('stripe')->info($message, $context);
    }

    private function logError(string $message, array $context = []): void
    {
        Log::channel('stripe')->error($message, $context);
    }
}
