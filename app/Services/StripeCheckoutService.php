<?php

namespace App\Services;

use App\Models\Report;
use App\Models\ReportPurchase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
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
    ) {
    }

    public function createCheckoutSession(Report $report): string
    {
        if (!$report->email || !$report->page_token) {
            throw new RuntimeException('Report email and page token are required before creating a checkout session.');
        }

        $priceId = $this->resolvePriceId($report->report_type);

        $purchase = ReportPurchase::create([
            'report_id' => $report->id,
            'report_type' => $report->report_type,
            'locale' => $report->locale,
            'email' => $report->email,
            'status' => 'checkout_created',
            'currency' => $this->currency(),
            'stripe_price_id' => $priceId,
            'metadata' => [
                'report_id' => $report->id,
                'page_token' => $report->page_token,
                'report_type' => $report->report_type,
                'locale' => $report->locale,
                'price_id' => $priceId,
            ],
            'checkout_started_at' => now(),
        ]);

        $payload = [
            'mode' => 'payment',
            'line_items' => [
                [
                    'price' => $priceId,
                    'quantity' => 1,
                ],
            ],
            'customer_email' => $report->email,
            'customer_creation' => 'always',
            'billing_address_collection' => 'required',
            'phone_number_collection' => [
                'enabled' => true,
            ],
            'metadata' => [
                'report_id' => (string) $report->id,
                'purchase_id' => (string) $purchase->id,
                'page_token' => $report->page_token,
                'report_type' => $report->report_type,
                'locale' => $report->locale,
                'price_id' => $priceId,
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

    public function markCheckoutSuccessReturn(Report $report, ?int $purchaseId, ?string $sessionId): void
    {
        $purchase = $this->findPurchase($report, $purchaseId, $sessionId);
        $sessionPaymentStatus = null;

        if ($sessionId) {
            try {
                $session = $this->retrieveCheckoutSession($sessionId);
                $sessionPaymentStatus = $session->payment_status;

                if ($purchase) {
                    $customerDetails = $this->normalizeStripeValue($session->customer_details);

                    $purchase->update([
                        'status' => $session->payment_status === 'paid'
                            ? 'payment_processing'
                            : $purchase->status,
                        'stripe_checkout_session_id' => $session->id,
                        'amount_subtotal' => $session->amount_subtotal,
                        'amount_total' => $session->amount_total,
                        'currency' => strtolower((string) ($session->currency ?: $purchase->currency ?: $this->currency())),
                        'customer_email' => $customerDetails['email'] ?? $session->customer_email ?? $purchase->email,
                        'customer_name' => $customerDetails['name'] ?? $purchase->customer_name,
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

        $purchase->report->update([
            'error_message' => null,
        ]);

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
            'expand' => ['payment_intent', 'customer'],
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

        $metadata = $this->normalizeStripeValue($session->metadata) ?? $purchase->metadata ?? [];
        $customerDetails = $this->normalizeStripeValue($session->customer_details);
        $paymentIntentPayload = is_object($session->payment_intent)
            ? $this->normalizeStripeValue($session->payment_intent)
            : null;

        $purchase->fill([
            'email' => $purchase->email ?: $purchase->report?->email,
            'status' => $status,
            'amount_subtotal' => $session->amount_subtotal,
            'amount_total' => $session->amount_total,
            'currency' => strtolower((string) ($session->currency ?: $purchase->currency ?: $this->currency())),
            'stripe_checkout_session_id' => $session->id,
            'stripe_payment_intent_id' => is_string($session->payment_intent)
                ? $session->payment_intent
                : $session->payment_intent?->id,
            'stripe_customer_id' => is_string($session->customer)
                ? $session->customer
                : $session->customer?->id,
            'stripe_price_id' => (string) ($metadata['price_id'] ?? $purchase->stripe_price_id ?? ''),
            'customer_email' => $customerDetails['email'] ?? $session->customer_email ?? $purchase->email,
            'customer_name' => $customerDetails['name'] ?? null,
            'customer_phone' => $customerDetails['phone'] ?? null,
            'customer_address' => is_array($customerDetails['address'] ?? null) ? $customerDetails['address'] : null,
            'customer_details' => $customerDetails,
            'checkout_session_payload' => $session->toArray(),
            'payment_intent_payload' => $paymentIntentPayload,
            'latest_webhook_event_id' => $eventId,
            'latest_webhook_event_type' => $eventType,
            'latest_webhook_payload' => $eventPayload,
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
            'currency' => strtolower((string) ($session->currency ?: $this->currency())),
            'stripe_checkout_session_id' => $session->id,
            'stripe_price_id' => $metadata['price_id'] ?? null,
            'metadata' => $metadata,
            'checkout_started_at' => $this->stripeTimestamp($session->created) ?: now(),
        ])->fresh('report');
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
        $path = route('checkout.success', ['pageToken' => $report->page_token], false);

        return $this->checkoutBaseUrl($report)
            . $path
            . '?session_id={CHECKOUT_SESSION_ID}&purchase='
            . $purchase->id;
    }

    private function cancelUrl(Report $report, ReportPurchase $purchase): string
    {
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