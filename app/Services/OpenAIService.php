<?php

namespace App\Services;

use App\Exceptions\OpenAIJsonException;
use App\Exceptions\OpenAIRequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private string $apiKey;
    private string $model = 'gpt-5.2';

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function validateUrl(string $url): array
    {
        $instructions = 'You are a URL accessibility checker. Use the web search tool to visit the given URL. Respond ONLY with a JSON object: {"accessible": true} if the URL is publicly reachable and contains a property listing, or {"accessible": false, "reason": "..."} if not.';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/responses', [
                'model' => $this->model,
                'instructions' => $instructions,
                'input' => $url,
                'tools' => [
                    ['type' => 'web_search_preview'],
                ],
            ]);

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
                Log::channel('report')->info('URL validation passed', ['url' => $url]);
                return ['success' => true];
            }

            $reason = $data['reason'] ?? 'URL is not accessible or does not contain a property listing.';
            Log::channel('report')->info('URL validation failed', ['url' => $url, 'reason' => $reason]);
            return ['success' => false, 'message' => $reason];

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

    public function generateReportData(string $url, string $prompt): array
    {
        try {
            Log::channel('report')->info($prompt);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.openai.com/v1/responses', [
                'model' => $this->model,
                'instructions' => $prompt,
                'input' => $url,
                'tools' => [
                    ['type' => 'web_search_preview'],
                ],
            ]);

            Log::channel('report')->info('Sending report generation request to OpenAI', ['response' => $response]);

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
