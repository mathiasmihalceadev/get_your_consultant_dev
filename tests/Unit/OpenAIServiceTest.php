<?php

namespace Tests\Unit;

use App\Services\OpenAIService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OpenAIServiceTest extends TestCase
{
    public function test_url_validation_prompt_explicitly_rejects_vehicle_listings(): void
    {
        config()->set('services.openai.key', 'test-key');

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'output' => [[
                    'type' => 'message',
                    'content' => [[
                        'type' => 'output_text',
                        'text' => '{"accessible": false, "reason_code": "not_property", "reason": "Vehicle listing."}',
                    ]],
                ]],
            ]),
        ]);

        $service = new OpenAIService();

        $service->validateUrl('https://example.com/cars/123', 'buying_living');

        Http::assertSent(function ($request): bool {
            if ($request->url() !== 'https://api.openai.com/v1/responses') {
                return false;
            }

            $payload = $request->data();
            $instructions = (string) ($payload['instructions'] ?? '');

            return str_contains($instructions, 'Treat vehicle listings and non-real-estate classifieds as not_property.')
                && str_contains($instructions, 'cars, motorcycles, trucks, vans, buses, boats, campers, tractors, auto parts')
                && str_contains($instructions, 'one specific residential listing')
                && str_contains($instructions, 'Expected transaction type: buying');
        });
    }
}