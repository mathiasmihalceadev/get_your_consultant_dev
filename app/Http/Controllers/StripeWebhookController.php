<?php

namespace App\Http\Controllers;

use App\Services\StripeCheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, StripeCheckoutService $stripe)
    {
        try {
            $result = $stripe->handleWebhook(
                $request->getContent(),
                $request->header('Stripe-Signature'),
            );
        } catch (UnexpectedValueException | SignatureVerificationException $e) {
            Log::channel('stripe')->warning('Rejected Stripe webhook payload', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['received' => false], 400);
        } catch (\Throwable $e) {
            Log::channel('stripe')->error('Stripe webhook handling failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['received' => false], 500);
        }

        return response()->json([
            'received' => true,
            'handled' => $result['handled'] ?? false,
        ]);
    }
}