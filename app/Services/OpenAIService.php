<?php

namespace App\Services;

use App\Exceptions\OpenAIJsonException;
use App\Exceptions\OpenAIRequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private const RESPONSES_ENDPOINT = 'https://api.openai.com/v1/responses';
    private string $apiKey;
    private string $urlValidationModel = 'gpt-5.5';
    private string $reportGenerationModel = 'gpt-5.5';
    private const URL_VALIDATION_REASON_CODES = [
        'accessible_property',
        'source_blocked',
        'not_property',
        'not_buying_property',
        'not_renting_property',
    ];
    private const LANDING_ESTIMATE_VALUATIONS = [
        'under_estimated',
        'close_to_estimated',
        'over_estimated',
    ];
    private const LANDING_ESTIMATE_RESULTS = [
        'Under estimated value',
        'Close to the estimated value',
        'Over the estimated value',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function validateUrl(string $url, string $reportType): array
    {
        $instructions = $this->urlValidationInstructions($reportType);

        try {
            $payload = [
                'model' => $this->urlValidationModel,
                'instructions' => $instructions,
                'input' => $url,
                'tools' => [
                    ['type' => 'web_search_preview'],
                ],
            ];

            $response = $this->sendResponsesRequest('url_validation', $payload, 30);

            if ($response->failed()) {
                Log::channel('report')->error('OpenAI URL validation request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response_body' => $response->body(),
                ]);
                throw new OpenAIRequestException('OpenAI request failed with status ' . $response->status());
            }

            $content = $this->extractOutputText($response->json());
            $content = $this->cleanJsonResponse($content);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::channel('report')->error('OpenAI URL validation JSON parse failed', [
                    'url' => $url,
                    'raw_response' => $content,
                ]);
                throw new OpenAIJsonException('Failed to parse OpenAI response as JSON');
            }

            if (!empty($data['accessible'])) {
                Log::channel('report')->info('URL validation passed', [
                    'url' => $url,
                    'report_type' => $reportType,
                    'reason_code' => $data['reason_code'] ?? 'accessible_property',
                ]);

                return [
                    'success' => true,
                    'reason_code' => 'accessible_property',
                ];
            }

            $reasonCode = $data['reason_code'] ?? 'source_blocked';

            if (!in_array($reasonCode, self::URL_VALIDATION_REASON_CODES, true)) {
                $reasonCode = 'source_blocked';
            }

            $reason = $data['reason'] ?? 'URL validation failed.';
            Log::channel('report')->info('URL validation failed', [
                'url' => $url,
                'report_type' => $reportType,
                'reason_code' => $reasonCode,
                'reason' => $reason,
            ]);

            return [
                'success' => false,
                'reason_code' => $reasonCode,
                'message' => $reason,
            ];

        } catch (OpenAIRequestException|OpenAIJsonException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::channel('report')->error('OpenAI URL validation exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw new OpenAIRequestException('OpenAI request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function urlValidationInstructions(string $reportType): string
    {
        $expectedTransaction = str_starts_with($reportType, 'buying_') ? 'buying' : 'renting';

        return <<<PROMPT
You are a validator for a residential real-estate report flow.

Use the web search tool to inspect the provided URL and classify it into exactly one of these reason codes:
- accessible_property: the URL is publicly reachable and is a residential property listing that matches the expected transaction type.
- source_blocked: the source cannot be analyzed reliably because it blocks automated access, requires authentication, rate-limits heavily, is unavailable, or the content cannot be opened.
- not_property: the URL is reachable but it is not a single public residential property listing page.
- not_buying_property: the URL is a property listing, but it is not a buying / for-sale listing while the expected transaction type is buying.
- not_renting_property: the URL is a property listing, but it is not a renting / for-rent listing while the expected transaction type is renting.

Expected transaction type: {$expectedTransaction}

Important rules:
- A page only qualifies as accessible_property when it clearly represents one specific residential listing, such as an apartment, studio, house, villa, duplex, or other home meant for people to live in.
- Treat search pages, category pages, homepages, news articles, blog posts, portals without a concrete listing, agent profile pages, and non-residential listings as not_property.
- Treat vehicle listings and non-real-estate classifieds as not_property. This includes cars, motorcycles, trucks, vans, buses, boats, campers, tractors, auto parts, and generic marketplaces where the main offer is not a residential property.
- If the page is ambiguous, mixed, or lacks clear evidence that the main offer is a single residential property listing, use not_property.
- Only use not_buying_property when the page is clearly a residential property listing but the listing is for rent while the expected transaction is buying.
- Only use not_renting_property when the page is clearly a residential property listing but the listing is for sale while the expected transaction is renting.
- If the page content cannot be inspected well enough because of login walls, bot protection, automation limits, broken pages, or unavailable content, use source_blocked.

Respond ONLY with strict JSON in one of these shapes:
{"accessible": true, "reason_code": "accessible_property"}
{"accessible": false, "reason_code": "source_blocked", "reason": "short explanation"}
{"accessible": false, "reason_code": "not_property", "reason": "short explanation"}
{"accessible": false, "reason_code": "not_buying_property", "reason": "short explanation"}
{"accessible": false, "reason_code": "not_renting_property", "reason": "short explanation"}
PROMPT;
    }

    public function estimateListingValue(string $url, string $locale): array
    {
        $instructions = $this->landingEstimateInstructions($locale);

        try {
            $payload = [
                'model' => $this->urlValidationModel,
                'instructions' => $instructions,
                'input' => $url,
                'reasoning' => [
                    'effort' => 'medium',
                ],
                'tools' => [
                    ['type' => 'web_search_preview'],
                ],
            ];

            $response = $this->sendResponsesRequest('landing_estimate_check', $payload, 90);

            if ($response->failed()) {
                Log::channel('report')->error('OpenAI landing estimate request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response_body' => $response->body(),
                ]);
                throw new OpenAIRequestException('OpenAI request failed with status ' . $response->status());
            }

            $content = $this->extractOutputText($response->json());
            $content = $this->cleanJsonResponse($content);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
                Log::channel('report')->error('OpenAI landing estimate JSON parse failed', [
                    'url' => $url,
                    'raw_response' => $content,
                ]);
                throw new OpenAIJsonException('Failed to parse OpenAI response as JSON');
            }

            if (empty($data['accessible'])) {
                $reasonCode = $data['reason_code'] ?? 'source_blocked';

                if (!in_array($reasonCode, ['source_blocked', 'not_property'], true)) {
                    $reasonCode = 'source_blocked';
                }

                return [
                    'success' => false,
                    'reason_code' => $reasonCode,
                    'message' => $data['reason'] ?? 'URL estimate check failed.',
                ];
            }

            $transactionType = $data['transaction_type'] ?? null;
            $reportType = match ($transactionType) {
                'buying' => 'buying_living',
                'renting' => 'rental_living',
                default => null,
            };

            $resultLabel = is_string($data['result'] ?? null) ? $data['result'] : null;
            $valuation = match ($resultLabel) {
                'Under estimated value' => 'under_estimated',
                'Close to the estimated value' => 'close_to_estimated',
                'Over the estimated value' => 'over_estimated',
                default => $data['valuation'] ?? null,
            };

            if (!$reportType || !in_array($valuation, self::LANDING_ESTIMATE_VALUATIONS, true)) {
                Log::channel('report')->error('OpenAI landing estimate returned invalid classification', [
                    'url' => $url,
                    'data' => $data,
                ]);

                throw new OpenAIJsonException('OpenAI response has an invalid estimate classification.');
            }

            return [
                'success' => true,
                'report_type' => $reportType,
                'transaction_type' => $transactionType,
                'valuation' => $valuation,
                'result' => $resultLabel && in_array($resultLabel, self::LANDING_ESTIMATE_RESULTS, true)
                    ? $resultLabel
                    : match ($valuation) {
                        'under_estimated' => 'Under estimated value',
                        'close_to_estimated' => 'Close to the estimated value',
                        'over_estimated' => 'Over the estimated value',
                    },
                'asking_price' => $data['asking_price'] ?? null,
                'estimated_value' => $data['estimated_value'] ?? null,
                'currency' => $data['currency'] ?? null,
                'difference_percent' => $data['difference_percent'] ?? null,
                'confidence' => $data['confidence'] ?? 'medium',
            ];

        } catch (OpenAIRequestException|OpenAIJsonException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::channel('report')->error('OpenAI landing estimate exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw new OpenAIRequestException('OpenAI request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function landingEstimateInstructions(string $locale): string
    {
        return <<<PROMPT
You are a residential real-estate listing pre-checker for GetYourConsultant.

Use the web search tool to inspect the provided URL. First validate the URL using the same standards as the full report flow, then create a fast indicative price/rent check.

Validation rules:
- The URL must be publicly reachable and must represent one specific residential property listing.
- Valid residential listings include apartments, studios, houses, villas, duplexes, or other homes meant for people to live in.
- Reject search pages, category pages, homepages, news articles, blog posts, agent profile pages, generic portal pages, non-residential listings, vehicle listings, and ambiguous marketplace pages.
- If the page cannot be inspected well enough because of login walls, bot protection, automation limits, broken pages, or unavailable content, use source_blocked.

For valid listings:
- Detect whether the listing is for buying/sale or renting.
- Extract the asking sale price or monthly rent from the listing.
- Estimate a fair market value or fair monthly rent using public comparable information, location context, property characteristics, and market indicators available from public sources.
- Classify the listing with exactly one result value:
  - "Under estimated value": asking price/rent is at least 5% below your estimated fair value/rent.
  - "Close to the estimated value": asking price/rent is within +/- 5% of your estimated fair value/rent.
  - "Over the estimated value": asking price/rent is at least 5% above your estimated fair value/rent.
- If the asking price/rent or enough property context cannot be found, return source_blocked instead of guessing.
- This is only a fast indicative check, not an official valuation.

Respond ONLY with strict JSON in one of these shapes:
{"accessible": false, "reason_code": "source_blocked", "reason": "short explanation"}
{"accessible": false, "reason_code": "not_property", "reason": "short explanation"}
{"accessible": true, "reason_code": "accessible_property", "transaction_type": "buying", "result": "Under estimated value", "asking_price": 120000, "estimated_value": 130000, "currency": "EUR", "difference_percent": -7.7, "confidence": "low|medium|high"}
{"accessible": true, "reason_code": "accessible_property", "transaction_type": "buying", "result": "Close to the estimated value", "asking_price": 120000, "estimated_value": 122000, "currency": "EUR", "difference_percent": -1.6, "confidence": "low|medium|high"}
{"accessible": true, "reason_code": "accessible_property", "transaction_type": "buying", "result": "Over the estimated value", "asking_price": 135000, "estimated_value": 120000, "currency": "EUR", "difference_percent": 12.5, "confidence": "low|medium|high"}
{"accessible": true, "reason_code": "accessible_property", "transaction_type": "renting", "result": "Under estimated value", "asking_price": 500, "estimated_value": 560, "currency": "EUR", "difference_percent": -10.7, "confidence": "low|medium|high"}
{"accessible": true, "reason_code": "accessible_property", "transaction_type": "renting", "result": "Close to the estimated value", "asking_price": 500, "estimated_value": 510, "currency": "EUR", "difference_percent": -2, "confidence": "low|medium|high"}
{"accessible": true, "reason_code": "accessible_property", "transaction_type": "renting", "result": "Over the estimated value", "asking_price": 600, "estimated_value": 520, "currency": "EUR", "difference_percent": 15.4, "confidence": "low|medium|high"}
PROMPT;
    }

    public function generateReportData(string $url, string $prompt): array
    {
        try {
            $payload = [
                'model' => $this->reportGenerationModel,
                'instructions' => $prompt,
                'input' => $url,
                'reasoning' => [
                    'effort' => 'high',
                ],
                'tools' => [
                    ['type' => 'web_search_preview'],
                ],
            ];

            $response = $this->sendResponsesRequest('report_generation', $payload, 600);

            if ($response->failed()) {
                Log::channel('report')->error('OpenAI report generation request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                    'response_body' => $response->body(),
                ]);
                throw new OpenAIRequestException('OpenAI request failed with status ' . $response->status());
            }

            $content = $this->extractOutputText($response->json());
            $content = $this->cleanJsonResponse($content);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::channel('report')->error('JSON parsing failed', [
                    'url' => $url,
                    'raw_response' => $content, 
                ]);
                throw new OpenAIJsonException('Failed to parse OpenAI response as JSON');
            }

            Log::channel('report')->info('Report data generated successfully', ['url' => $url]);
            return $data;

        } catch (OpenAIRequestException|OpenAIJsonException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::channel('report')->error('OpenAI report generation exception', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            throw new OpenAIRequestException('OpenAI request failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private function sendResponsesRequest(string $operation, array $payload, int $timeoutSeconds): Response
    {
        $this->logOpenAIRequest($operation, $payload, $timeoutSeconds);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout($timeoutSeconds)->post(self::RESPONSES_ENDPOINT, $payload);

        $this->logOpenAIResponse($operation, $response);

        return $response;
    }

    private function logOpenAIRequest(string $operation, array $payload, int $timeoutSeconds): void
    {
        Log::channel('report')->info('OpenAI request', [
            'operation' => $operation,
            'endpoint' => self::RESPONSES_ENDPOINT,
            'timeout_seconds' => $timeoutSeconds,
            'payload' => $payload,
        ]);
    }

    private function logOpenAIResponse(string $operation, Response $response): void
    {
        Log::channel('report')->info('OpenAI response', [
            'operation' => $operation,
            'endpoint' => self::RESPONSES_ENDPOINT,
            'status' => $response->status(),
            'successful' => $response->successful(),
            'body' => $this->decodeResponseBody($response->body()),
        ]);
    }

    private function decodeResponseBody(string $body): mixed
    {
        $decoded = json_decode($body, true);

        if (json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        return $body;
    }

    private function extractOutputText(array $responseData): ?string
    {
        foreach ($responseData['output'] ?? [] as $item) {
            if (($item['type'] ?? '') === 'message') {
                foreach ($item['content'] ?? [] as $content) {
                    if (($content['type'] ?? '') === 'output_text') {
                        return $content['text'];
                    }
                }
            }
        }

        return null;
    }

    private function cleanJsonResponse(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }

        $content = trim($content);

        // Strip markdown code fences if present
        if (str_starts_with($content, '```')) {
            // Remove opening fence (e.g. ```json or ```)
            $firstNewline = strpos($content, "\n");
            if ($firstNewline !== false) {
                $content = substr($content, $firstNewline + 1);
            }
            // Remove closing fence
            $lastFence = strrpos($content, '```');
            if ($lastFence !== false) {
                $content = substr($content, 0, $lastFence);
            }
            $content = trim($content);
        }

        // If all else fails, extract the first { ... } or [ ... ] block
        if (!str_starts_with($content, '{') && !str_starts_with($content, '[')) {
            $start = strpos($content, '{');
            if ($start === false) {
                $start = strpos($content, '[');
            }
            if ($start !== false) {
                $content = substr($content, $start);
            }
        }

        return trim($content);
    }
}
