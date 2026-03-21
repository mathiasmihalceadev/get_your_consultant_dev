<?php

namespace App\Services;

use App\Exceptions\OpenAIJsonException;
use App\Exceptions\OpenAIRequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private string $apiKey;
    private string $model = 'gpt-4o';
    private int $maxTokens = 4000;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
    }

    public function validateUrl(string $url): array
    {
        $systemPrompt = 'You are a URL accessibility checker. The user will give you a URL. Respond ONLY with a JSON object: {"accessible": true} if the URL is publicly reachable and contains a property listing, or {"accessible": false, "reason": "..."} if not.';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'max_tokens' => 500,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $url],
                ],
            ]);

            if ($response->failed()) {
                Log::channel('report')->error('OpenAI URL validation request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);
                throw new OpenAIRequestException('OpenAI request failed with status ' . $response->status());
            }

            $content = $response->json('choices.0.message.content');
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
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->model,
                'max_tokens' => $this->maxTokens,
                'messages' => [
                    ['role' => 'system', 'content' => $prompt],
                    ['role' => 'user', 'content' => $url],
                ],
            ]);

            if ($response->failed()) {
                Log::channel('report')->error('OpenAI report generation request failed', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);
                throw new OpenAIRequestException('OpenAI request failed with status ' . $response->status());
            }

            $content = $response->json('choices.0.message.content');
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
}
