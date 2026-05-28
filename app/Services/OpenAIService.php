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
    private string $urlValidationModel = 'gpt-5.4-mini';
    private string $reportGenerationModel = 'gpt-5.5';
    private const URL_VALIDATION_REASON_CODES = [
        'accessible_property',
        'source_blocked',
        'not_property',
        'not_buying_property',
        'not_renting_property',
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
