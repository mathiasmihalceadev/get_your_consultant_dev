<?php

namespace App\Services;

use App\Exceptions\SmartBillException;
use App\Models\ReportPurchase;
use App\Models\SmartBillInvoice;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class SmartBillService
{
    public function syncPurchase(ReportPurchase $purchase): SmartBillInvoice
    {
        $purchase->loadMissing('report');

        if (!$purchase->report) {
            throw new RuntimeException('Cannot issue a SmartBill invoice for a purchase without a report.');
        }

        $invoice = SmartBillInvoice::firstOrCreate(
            ['report_purchase_id' => $purchase->id],
            [
                'report_id' => $purchase->report_id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'company_vat_code' => (string) config('services.smartbill.company_vat_code', ''),
            ],
        );

        $invoice->forceFill([
            'report_id' => $purchase->report_id,
            'company_vat_code' => (string) config('services.smartbill.company_vat_code', ''),
            'invoice_currency' => $this->invoiceCurrency($purchase),
            'invoice_language' => $this->invoiceLanguage($purchase),
            'last_attempt_at' => now(),
        ])->save();

        if (!$this->hasIssuedInvoice($purchase, $invoice)) {
            $this->issueInvoice($purchase, $invoice);
            $invoice->refresh();
        }

        if ($this->shouldRegisterPayment($purchase, $invoice)) {
            $this->registerPayment($purchase, $invoice);
        }

        return $invoice->fresh();
    }

    public function downloadInvoicePdf(SmartBillInvoice $invoice): ?string
    {
        $downloadUrl = $this->resolveInvoicePdfDownloadUrl($invoice);

        if ($downloadUrl === null) {
            return null;
        }

        $this->assertPdfDownloadConfigured();

        $requestContext = array_filter([
            'operation' => 'download_invoice_pdf',
            'smart_bill_invoice_id' => $invoice->id,
            'report_id' => $invoice->report_id,
            'purchase_id' => $invoice->report_purchase_id,
            'invoice_series' => $invoice->invoice_series,
            'invoice_number' => $invoice->invoice_number,
            'method' => 'GET',
            'url' => $downloadUrl,
        ], static fn (mixed $value): bool => $value !== null && $value !== '');

        Log::channel('smartbill')->info('SmartBill request', $requestContext);

        try {
            $response = Http::withBasicAuth($this->username(), $this->token())
                ->withHeaders([
                    'Accept' => 'application/octet-stream, application/xml',
                ])
                ->timeout($this->timeout())
                ->get($downloadUrl);
        } catch (ConnectionException $exception) {
            Log::channel('smartbill')->error('SmartBill transport error', [
                ...$requestContext,
                'error' => $exception->getMessage(),
            ]);

            throw new SmartBillException(
                'SmartBill invoice PDF request failed before receiving a response: ' . $exception->getMessage(),
                true,
                null,
                null,
                $exception,
            );
        }

        $rawBody = $response->body();

        Log::channel('smartbill')->info('SmartBill response', [
            ...$requestContext,
            'status' => $response->status(),
            'content_type' => $response->header('Content-Type'),
            'body' => $this->summarizePdfResponseBody($rawBody),
        ]);

        if ($response->failed()) {
            throw new SmartBillException(
                $this->errorTextFromRawBody($rawBody)
                    ?? $response->reason()
                    ?? 'SmartBill invoice PDF request failed.',
                $this->isRetryableStatus($response->status()),
                $response->status(),
                $this->normalizePayloadForStorage($rawBody),
            );
        }

        $pdfContent = $this->extractPdfContent($rawBody);

        if ($pdfContent === null) {
            throw new SmartBillException(
                $this->errorTextFromRawBody($rawBody)
                    ?? 'SmartBill invoice PDF response did not contain a valid PDF document.',
                false,
                $response->status(),
                $this->normalizePayloadForStorage($rawBody),
            );
        }

        return $pdfContent;
    }

    private function issueInvoice(ReportPurchase $purchase, SmartBillInvoice $invoice): void
    {
        $payload = $this->buildInvoicePayload($purchase);

        $invoice->forceFill([
            'invoice_request_payload' => $payload,
            'last_attempt_at' => now(),
            'error_message' => null,
        ])->save();

        try {
            $response = $this->request('post', 'invoice', ['json' => $payload], [
                'operation' => 'issue_invoice',
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
                'smart_bill_invoice_id' => $invoice->id,
            ]);
        } catch (SmartBillException $exception) {
            $invoice->forceFill([
                'status' => 'failed',
                'invoice_response_payload' => $this->normalizePayloadForStorage($exception->responseBody),
                'last_attempt_at' => now(),
                'error_message' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }

        $result = $this->unwrapResponse($response);
        $series = trim((string) ($result['series'] ?? ''));
        $number = trim((string) ($result['number'] ?? ''));
        $isDraftInvoice = $this->acceptsDraftInvoiceResponse($purchase, $series, $number);

        if ($series === '' || ($number === '' && !$isDraftInvoice)) {
            $exception = new SmartBillException(
                'SmartBill invoice response did not include the issued series and number.',
                false,
                200,
                $response,
            );

            $invoice->forceFill([
                'status' => 'failed',
                'invoice_response_payload' => $this->normalizePayloadForStorage($response),
                'last_attempt_at' => now(),
                'error_message' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }

        $documentUrl = $this->nullableString($result['documentUrl'] ?? null);
        $documentViewUrl = $this->nullableString($result['documentViewUrl'] ?? null);
        $downloadUrl = $this->invoicePdfDownloadUrl($series, $number !== '' ? $number : null);

        $invoice->forceFill([
            'status' => $isDraftInvoice ? 'draft' : 'issued',
            'invoice_series' => $series,
            'invoice_number' => $number !== '' ? $number : null,
            'document_id' => $this->nullableString($result['documentId'] ?? null),
            'document_url' => $documentUrl,
            'document_view_url' => $documentViewUrl,
            'file_url' => $documentViewUrl ?: $documentUrl,
            'download_url' => $downloadUrl,
            'invoice_response_payload' => $this->normalizePayloadForStorage($response),
            'issued_at' => $purchase->paid_at ?: now(),
            'last_attempt_at' => now(),
            'payment_status' => $isDraftInvoice ? 'skipped' : $invoice->payment_status,
            'error_message' => null,
        ])->save();
    }

    private function hasIssuedInvoice(ReportPurchase $purchase, SmartBillInvoice $invoice): bool
    {
        if (!$invoice->invoice_series) {
            return false;
        }

        if ($this->isDraftInvoice($purchase, $invoice)) {
            return true;
        }

        return (bool) $invoice->invoice_number;
    }

    private function shouldRegisterPayment(ReportPurchase $purchase, SmartBillInvoice $invoice): bool
    {
        if ($this->isDraftInvoice($purchase, $invoice)) {
            return false;
        }

        return $invoice->payment_status !== 'registered';
    }

    private function acceptsDraftInvoiceResponse(ReportPurchase $purchase, string $series, string $number): bool
    {
        return (bool) $purchase->report?->is_test
            && $series !== ''
            && $number === '';
    }

    private function isDraftInvoice(ReportPurchase $purchase, SmartBillInvoice $invoice): bool
    {
        return (bool) $purchase->report?->is_test
            && $invoice->status === 'draft';
    }

    private function registerPayment(ReportPurchase $purchase, SmartBillInvoice $invoice): void
    {
        $payload = $this->buildPaymentPayload($purchase, $invoice);

        $invoice->forceFill([
            'payment_request_payload' => $payload,
            'payment_type' => $this->paymentType(),
            'last_attempt_at' => now(),
            'error_message' => null,
        ])->save();

        try {
            $response = $this->request('post', 'payment', ['json' => $payload], [
                'operation' => 'register_payment',
                'purchase_id' => $purchase->id,
                'report_id' => $purchase->report_id,
                'smart_bill_invoice_id' => $invoice->id,
                'invoice_series' => $invoice->invoice_series,
                'invoice_number' => $invoice->invoice_number,
            ]);
        } catch (SmartBillException $exception) {
            $invoice->forceFill([
                'status' => 'issued',
                'payment_status' => 'failed',
                'payment_response_payload' => $this->normalizePayloadForStorage($exception->responseBody),
                'last_attempt_at' => now(),
                'error_message' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }

        $invoice->forceFill([
            'status' => 'completed',
            'payment_status' => 'registered',
            'payment_response_payload' => $this->normalizePayloadForStorage($response),
            'payment_registered_at' => $purchase->paid_at ?: now(),
            'last_attempt_at' => now(),
            'error_message' => null,
        ])->save();
    }

    private function buildInvoicePayload(ReportPurchase $purchase): array
    {
        $date = ($purchase->paid_at ?: now())->format('Y-m-d');
        $report = $purchase->report;

        $payload = [
            'companyVatCode' => $this->companyVatCode(),
            'client' => $this->buildClientPayload($purchase),
            'isDraft' => (bool) $report?->is_test
                && (bool) config('services.smartbill.invoice.test_draft', true),
            'issueDate' => $date,
            'seriesName' => $this->invoiceSeries(),
            'currency' => $this->invoiceCurrency($purchase),
            'language' => $this->invoiceLanguage($purchase),
            'precision' => 2,
            'dueDate' => $date,
            'deliveryDate' => $date,
            'observations' => sprintf(
                '%s #%d / Purchase #%d / Stripe payment intent %s',
                $report?->is_test ? 'Billing test report' : 'Report',
                $purchase->report_id,
                $purchase->id,
                $purchase->stripe_payment_intent_id ?: 'n/a',
            ),
            'products' => [$this->buildProductPayload($purchase)],
        ];

        if (($exchangeRate = $this->exchangeRate($purchase)) !== null) {
            $payload['exchangeRate'] = $exchangeRate;
        }

        return $payload;
    }

    private function buildPaymentPayload(ReportPurchase $purchase, SmartBillInvoice $invoice): array
    {
        return [
            'companyVatCode' => $this->companyVatCode(),
            'issueDate' => ($purchase->paid_at ?: now())->format('Y-m-d'),
            'type' => $this->paymentType(),
            'isCash' => false,
            'useInvoiceDetails' => true,
            'invoicesList' => [[
                'seriesName' => $invoice->invoice_series,
                'number' => $invoice->invoice_number,
            ]],
        ];
    }

    private function buildClientPayload(ReportPurchase $purchase): array
    {
        $address = $this->normalizedAddress($purchase);
        $vatCode = $this->customerVatCode($purchase);

        return array_filter([
            'name' => $this->customerName($purchase),
            'vatCode' => $vatCode,
            'isTaxPayer' => $this->customerIsTaxPayer($purchase, $vatCode),
            'address' => $address['address'],
            'city' => $address['city'],
            'county' => $address['county'],
            'country' => $address['country'],
            'email' => $purchase->customer_email ?: $purchase->email,
            'phone' => $purchase->customer_phone,
            'contact' => $purchase->customer_name,
            'saveToDb' => false,
        ], static fn (mixed $value): bool => $value !== null && $value !== '');
    }

    private function buildProductPayload(ReportPurchase $purchase): array
    {
        $payload = [
            'name' => $this->productName($purchase),
            'code' => $this->productCode($purchase),
            'productDescription' => $this->productDescription($purchase),
            'isDiscount' => false,
            'measuringUnitName' => 'buc',
            'currency' => $this->invoiceCurrency($purchase),
            'quantity' => 1,
            'price' => $this->amountForPurchase($purchase),
            'saveToDb' => false,
            'isService' => true,
        ];

        if (($taxIncluded = $this->taxIncluded()) !== null) {
            $payload['isTaxIncluded'] = $taxIncluded;
        }

        if (($taxName = $this->taxName()) !== null) {
            $payload['taxName'] = $taxName;
        }

        if (($taxPercentage = $this->taxPercentage()) !== null) {
            $payload['taxPercentage'] = $taxPercentage;
        }

        if ($this->invoiceLanguage($purchase) !== 'RO') {
            $payload['translatedName'] = $this->productName($purchase, 'en');
            $payload['translatedMeasuringUnit'] = 'piece';
        }

        return $payload;
    }

    private function productCode(ReportPurchase $purchase): string
    {
        if ($purchase->report?->is_test) {
            return 'TEST-STRIPE-SMARTBILL';
        }

        $reportType = Str::upper(str_replace('_', '-', (string) $purchase->report_type));

        return $reportType !== ''
            ? 'REPORT-' . $reportType
            : 'REPORT-' . $purchase->id;
    }

    private function request(string $method, string $path, array $options, array $context = []): array
    {
        $this->assertConfigured();

        $url = $this->urlFor($path);
        $requestContext = array_filter([
            ...$context,
            'method' => strtoupper($method),
            'url' => $url,
            'payload' => $options['json'] ?? null,
            'query' => $options['query'] ?? null,
        ], static fn (mixed $value): bool => $value !== null);

        Log::channel('smartbill')->info('SmartBill request', $requestContext);

        try {
            $response = Http::withBasicAuth($this->username(), $this->token())
                ->acceptJson()
                ->asJson()
                ->timeout($this->timeout())
                ->send($method, $url, $options);
        } catch (ConnectionException $exception) {
            Log::channel('smartbill')->error('SmartBill transport error', [
                ...$requestContext,
                'error' => $exception->getMessage(),
            ]);

            throw new SmartBillException(
                'SmartBill request failed before receiving a response: ' . $exception->getMessage(),
                true,
                null,
                null,
                $exception,
            );
        }

        $body = $this->decodeResponse($response);

        Log::channel('smartbill')->info('SmartBill response', [
            ...$requestContext,
            'status' => $response->status(),
            'body' => $body,
        ]);

        if ($response->failed()) {
            throw $this->exceptionFromResponse($response, $body);
        }

        $errorText = $this->errorTextFromBody($body);

        if ($errorText !== null) {
            throw new SmartBillException(
                $errorText,
                false,
                $response->status(),
                $body,
            );
        }

        return $this->normalizePayloadForStorage($body) ?? [];
    }

    private function decodeResponse(Response $response): mixed
    {
        $decoded = $response->json();

        return $decoded !== null ? $decoded : $response->body();
    }

    private function exceptionFromResponse(Response $response, mixed $body): SmartBillException
    {
        $status = $response->status();
        $message = $this->errorTextFromBody($body)
            ?? $response->reason()
            ?? 'SmartBill request failed.';

        return new SmartBillException(
            $message,
            $this->isRetryableStatus($status),
            $status,
            $body,
        );
    }

    private function unwrapResponse(array $payload): array
    {
        foreach (['sbcResponse', 'sbcInvoicePaymentStatusResponse', 'Response'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return $payload[$key];
            }
        }

        return $payload;
    }

    private function errorTextFromBody(mixed $body): ?string
    {
        if (!is_array($body)) {
            $rawBody = is_string($body) ? trim($body) : '';

            return $rawBody !== '' ? $rawBody : null;
        }

        $primary = $this->unwrapResponse($body);
        $errorText = trim((string) ($primary['errorText'] ?? $body['errorText'] ?? ''));

        if ($errorText !== '') {
            return $errorText;
        }

        $message = trim((string) ($primary['message'] ?? $body['message'] ?? ''));

        return $message !== '' ? $message : null;
    }

    private function normalizePayloadForStorage(mixed $payload): ?array
    {
        if ($payload === null) {
            return null;
        }

        if (is_array($payload)) {
            return $payload;
        }

        if (is_object($payload)) {
            $decoded = json_decode(json_encode($payload), true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        $stringPayload = is_string($payload)
            ? $payload
            : json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return ['raw' => $stringPayload];
    }

    private function normalizedAddress(ReportPurchase $purchase): array
    {
        $address = $purchase->customer_address ?? [];
        $lineParts = array_values(array_filter([
            $address['line1'] ?? null,
            $address['line2'] ?? null,
            $address['postal_code'] ?? null,
        ], static fn (?string $value): bool => (string) $value !== ''));

        $country = $this->resolveCountryName($address['country'] ?? null);

        return [
            'address' => $lineParts !== [] ? implode(', ', $lineParts) : '-',
            'city' => trim((string) ($address['city'] ?? '')) ?: '-',
            'county' => trim((string) ($address['state'] ?? '')) ?: null,
            'country' => $country,
        ];
    }

    private function customerName(ReportPurchase $purchase): string
    {
        $details = $purchase->customer_details ?? [];
        $preferredName = trim((string) (
            $details['business_name']
            ?? $purchase->customer_name
            ?? $details['name']
            ?? $details['individual_name']
            ?? ''
        ));

        if ($preferredName !== '') {
            return $preferredName;
        }

        $name = trim((string) ($purchase->customer_name ?: ''));

        if ($name !== '') {
            return $name;
        }

        $email = trim((string) ($purchase->customer_email ?: $purchase->email ?: ''));

        if ($email !== '') {
            return Str::title(str_replace(['.', '_', '-'], ' ', Str::before($email, '@')));
        }

        return 'Customer #' . $purchase->id;
    }

    private function customerVatCode(ReportPurchase $purchase): string
    {
        foreach ($this->customerTaxIds($purchase) as $taxId) {
            $normalized = $this->normalizeCustomerVatCode($taxId['value'] ?? null);

            if ($normalized !== null) {
                return $normalized;
            }
        }

        return '-';
    }

    private function customerIsTaxPayer(ReportPurchase $purchase, string $vatCode): bool
    {
        if ($vatCode === '-') {
            return false;
        }

        foreach ($this->customerTaxIds($purchase) as $taxId) {
            if ($this->isVatTaxIdType($taxId['type'] ?? null)) {
                return true;
            }
        }

        return false;
    }

    private function customerTaxIds(ReportPurchase $purchase): array
    {
        $details = $purchase->customer_details ?? [];
        $taxIds = $details['tax_ids'] ?? [];

        return is_array($taxIds) ? $taxIds : [];
    }

    private function normalizeCustomerVatCode(mixed $value): ?string
    {
        $vatCode = strtoupper(trim((string) ($value ?? '')));
        $vatCode = preg_replace('/\s+/', '', $vatCode ?? '');

        return $vatCode !== '' ? $vatCode : null;
    }

    private function isVatTaxIdType(mixed $type): bool
    {
        $normalizedType = strtolower(trim((string) ($type ?? '')));

        return $normalizedType !== '' && str_contains($normalizedType, 'vat');
    }

    private function productName(ReportPurchase $purchase, ?string $locale = null): string
    {
        $locale ??= $purchase->locale === 'ro' ? 'ro' : 'en';

        if ($purchase->report?->is_test) {
            return $locale === 'ro'
                ? 'Test Stripe + SmartBill'
                : 'Stripe + SmartBill test';
        }

        return match ([$purchase->report_type, $locale]) {
            ['buying_living', 'ro'] => 'Raport cumparare locuinta',
            ['buying_business', 'ro'] => 'Raport cumparare spatiu business',
            ['rental_living', 'ro'] => 'Raport inchiriere locuinta',
            ['rental_business', 'ro'] => 'Raport inchiriere spatiu business',
            ['buying_living', 'en'] => 'Residential purchase report',
            ['buying_business', 'en'] => 'Business property purchase report',
            ['rental_living', 'en'] => 'Residential rental report',
            ['rental_business', 'en'] => 'Business property rental report',
            default => Str::title(str_replace('_', ' ', $purchase->report_type)),
        };
    }

    private function productDescription(ReportPurchase $purchase): string
    {
        if ($purchase->report?->is_test) {
            return $purchase->locale === 'ro'
                ? 'Flux de test pentru Stripe si SmartBill. Nu se genereaza raportul final.'
                : 'Billing test flow for Stripe and SmartBill. No final report is generated.';
        }

        return $purchase->locale === 'ro'
            ? 'Raport digital pentru analiza unei proprietati imobiliare.'
            : 'Digital report for the analysis of a real-estate property.';
    }

    private function amountForPurchase(ReportPurchase $purchase): float
    {
        $minorAmount = $purchase->amount_total
            ?? $purchase->checkout_amount_minor
            ?? $purchase->base_amount_minor;

        if ($minorAmount === null) {
            throw new RuntimeException('Cannot issue a SmartBill invoice without a resolved purchase amount.');
        }

        return round(((int) $minorAmount) / 100, 2);
    }

    private function exchangeRate(ReportPurchase $purchase): ?float
    {
        if ($this->invoiceCurrency($purchase) === 'RON') {
            return null;
        }

        return $purchase->exchange_rate !== null
            ? round((float) $purchase->exchange_rate, 6)
            : null;
    }

    private function paymentType(): string
    {
        $type = trim((string) config('services.smartbill.invoice.payment_type', 'Card online'));

        return $type !== '' ? $type : 'Card online';
    }

    private function taxName(): ?string
    {
        $taxName = trim((string) config('services.smartbill.invoice.tax_name', ''));

        return $taxName !== '' ? $taxName : null;
    }

    private function taxPercentage(): ?float
    {
        $taxPercentage = config('services.smartbill.invoice.tax_percentage');

        if ($taxPercentage === null || $taxPercentage === '') {
            return null;
        }

        if (!is_numeric($taxPercentage)) {
            return null;
        }

        return (float) $taxPercentage;
    }

    private function taxIncluded(): ?bool
    {
        $value = config('services.smartbill.invoice.tax_included');

        if ($value === null || $value === '') {
            return null;
        }

        if (is_bool($value)) {
            return $value;
        }

        $parsed = filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

        return $parsed;
    }

    private function invoiceCurrency(ReportPurchase $purchase): string
    {
        return strtoupper((string) ($purchase->paid_currency ?: $purchase->currency ?: 'RON'));
    }

    private function invoiceLanguage(ReportPurchase $purchase): string
    {
        return $purchase->locale === 'ro' ? 'RO' : 'EN';
    }

    private function assertConfigured(): void
    {
        $missing = [];

        if ($this->username() === '') {
            $missing[] = 'SMARTBILL_USERNAME';
        }

        if ($this->token() === '') {
            $missing[] = 'SMARTBILL_TOKEN';
        }

        if ($this->companyVatCode() === '') {
            $missing[] = 'SMARTBILL_COMPANY_VAT_CODE';
        }

        if ($this->invoiceSeries() === '') {
            $missing[] = 'SMARTBILL_INVOICE_SERIES';
        }

        if ($missing === []) {
            return;
        }

        throw new SmartBillException(
            'SmartBill is not fully configured. Missing: ' . implode(', ', $missing) . '.',
            false,
        );
    }

    private function assertPdfDownloadConfigured(): void
    {
        $missing = [];

        if ($this->username() === '') {
            $missing[] = 'SMARTBILL_USERNAME';
        }

        if ($this->token() === '') {
            $missing[] = 'SMARTBILL_TOKEN';
        }

        if ($this->companyVatCode() === '') {
            $missing[] = 'SMARTBILL_COMPANY_VAT_CODE';
        }

        if ($missing === []) {
            return;
        }

        throw new SmartBillException(
            'SmartBill invoice PDF download is not fully configured. Missing: ' . implode(', ', $missing) . '.',
            false,
        );
    }

    private function username(): string
    {
        return trim((string) config('services.smartbill.username', ''));
    }

    private function token(): string
    {
        return trim((string) config('services.smartbill.token', ''));
    }

    private function companyVatCode(): string
    {
        return trim((string) config('services.smartbill.company_vat_code', ''));
    }

    private function invoiceSeries(): string
    {
        return trim((string) config('services.smartbill.invoice.series', ''));
    }

    private function invoicePdfDownloadUrl(string $series, ?string $number): ?string
    {
        $normalizedSeries = trim($series);
        $normalizedNumber = trim((string) ($number ?? ''));

        if ($normalizedSeries === '' || $normalizedNumber === '') {
            return null;
        }

        return $this->urlFor('invoice/pdf') . '?' . http_build_query([
            'cif' => $this->companyVatCode(),
            'seriesname' => $normalizedSeries,
            'number' => $normalizedNumber,
        ], '', '&', PHP_QUERY_RFC3986);
    }

    private function resolveInvoicePdfDownloadUrl(SmartBillInvoice $invoice): ?string
    {
        $downloadUrl = $this->nullableString($invoice->download_url);

        if ($downloadUrl !== null) {
            return $downloadUrl;
        }

        $downloadUrl = $this->invoicePdfDownloadUrl(
            (string) $invoice->invoice_series,
            $invoice->invoice_number,
        );

        if ($downloadUrl === null) {
            return null;
        }

        $invoice->download_url = $downloadUrl;

        if ($invoice->exists) {
            $invoice->save();
        }

        return $downloadUrl;
    }

    private function timeout(): int
    {
        return max(1, (int) config('services.smartbill.timeout', 20));
    }

    private function urlFor(string $path): string
    {
        return rtrim((string) config('services.smartbill.base_url', 'https://ws.smartbill.ro/SBORO/api'), '/')
            . '/'
            . ltrim($path, '/');
    }

    private function isRetryableStatus(int $status): bool
    {
        return $status === 403 || $status === 429 || $status >= 500;
    }

    private function summarizePdfResponseBody(string $rawBody): array
    {
        $pdfContent = $this->extractPdfContent($rawBody);

        if ($pdfContent !== null) {
            return [
                'kind' => 'pdf',
                'bytes' => strlen($pdfContent),
            ];
        }

        $body = trim($rawBody);

        return [
            'kind' => 'text',
            'preview' => Str::limit($body, 1000),
        ];
    }

    private function extractPdfContent(string $rawBody): ?string
    {
        if ($rawBody === '') {
            return null;
        }

        $trimmedBody = ltrim($rawBody);

        if (str_starts_with($trimmedBody, '%PDF-')) {
            return $trimmedBody;
        }

        $normalizedBase64 = preg_replace('/\s+/', '', $rawBody);

        if (!is_string($normalizedBase64) || $normalizedBase64 === '') {
            return null;
        }

        $decoded = base64_decode($normalizedBase64, true);

        if ($decoded === false) {
            return null;
        }

        return str_starts_with($decoded, '%PDF-') ? $decoded : null;
    }

    private function errorTextFromRawBody(string $rawBody): ?string
    {
        $body = trim($rawBody);

        if ($body === '') {
            return null;
        }

        $decodedJson = json_decode($body, true);

        if (is_array($decodedJson)) {
            return $this->errorTextFromBody($decodedJson);
        }

        if (preg_match('/<errorText>(.*?)<\/errorText>/si', $body, $matches) === 1) {
            $message = trim(html_entity_decode(strip_tags($matches[1]), ENT_QUOTES | ENT_HTML5));

            if ($message !== '') {
                return $message;
            }
        }

        return $body;
    }

    private function resolveCountryName(mixed $country): ?string
    {
        $countryCode = strtoupper(trim((string) $country));

        if ($countryCode === '') {
            return null;
        }

        return match ($countryCode) {
            'RO' => 'Romania',
            'DE' => 'Germany',
            'ES' => 'Spain',
            'FR' => 'France',
            'GB' => 'United Kingdom',
            'IT' => 'Italy',
            'NL' => 'Netherlands',
            'US' => 'United States',
            'CA' => 'Canada',
            default => $countryCode,
        };
    }

    private function nullableString(mixed $value): ?string
    {
        $stringValue = trim((string) ($value ?? ''));

        return $stringValue !== '' ? $stringValue : null;
    }
}