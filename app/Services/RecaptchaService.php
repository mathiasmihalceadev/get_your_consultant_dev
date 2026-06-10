<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecaptchaService
{
    public function enabled(): bool
    {
        return (bool) config('services.recaptcha.enabled')
            && $this->siteKey() !== ''
            && $this->secretKey() !== '';
    }

    public function siteKey(): string
    {
        return (string) config('services.recaptcha.site_key', '');
    }

    public function verify(Request $request, string $expectedAction): bool
    {
        if (!$this->enabled()) {
            return true;
        }

        $token = (string) $request->input('recaptcha_token', '');

        if ($token === '') {
            $this->logFailure($request, 'missing_token');

            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout((int) config('services.recaptcha.timeout', 5))
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $this->secretKey(),
                    'response' => $token,
                    'remoteip' => $request->ip(),
                ]);
        } catch (\Throwable $e) {
            Log::channel('audit')->warning('reCAPTCHA verification request failed', [
                'action' => $expectedAction,
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
            ]);

            return false;
        }

        $payload = $response->json();

        if (!is_array($payload)) {
            $this->logFailure($request, 'invalid_response', [
                'status' => $response->status(),
            ]);

            return false;
        }

        $success = (bool) ($payload['success'] ?? false);
        $score = (float) ($payload['score'] ?? 0);
        $action = (string) ($payload['action'] ?? '');
        $minimumScore = (float) config('services.recaptcha.minimum_score', 0.5);

        if ($success && $action === $expectedAction && $score >= $minimumScore) {
            return true;
        }

        $this->logFailure($request, 'verification_failed', [
            'success' => $success,
            'score' => $score,
            'minimum_score' => $minimumScore,
            'action' => $action,
            'expected_action' => $expectedAction,
            'hostname' => $payload['hostname'] ?? null,
            'error_codes' => $payload['error-codes'] ?? [],
        ]);

        return false;
    }

    private function secretKey(): string
    {
        return (string) config('services.recaptcha.secret_key', '');
    }

    private function logFailure(Request $request, string $reason, array $context = []): void
    {
        Log::channel('audit')->warning('reCAPTCHA contact form verification failed', [
            'reason' => $reason,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            ...$context,
        ]);
    }
}
