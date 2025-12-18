<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Transfer;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, OrderService $orderService)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = $secret
                ? Webhook::constructEvent($payload, $sigHeader, $secret)
                : json_decode($payload);
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type !== 'payment_intent.succeeded') {
            return response()->json(['status' => 'ignored']);
        }

        $intent = $event->data->object;

        $order = Order::with('orderItem.Listing.user')
            ->where('payment_intent_id', $intent->id)
            ->first();

        if (!$order) {
            Log::warning("Stripe webhook: order not found for intent {$intent->id}");
            return response()->json(['status' => 'ok']);
        }

        if ($order->statusas === Order::STATUS_PAID) {
            return response()->json(['status' => 'ok']);
        }

        $splits = $order->payment_intents ?? [];

        if (!is_array($splits) || empty($splits)) {
            Log::error("Order {$order->id} missing split data");
            return response()->json(['status' => 'error'], 500);
        }

        $transferGroup = 'order_' . $order->id;

        foreach ($splits as $index => $split) {
            if (!empty($split['transfer_id'])) {
                continue; 
            }

            try {
                $transfer = Transfer::create([
                    'amount' => (int) $split['seller_amount_cents'],
                    'currency' => 'eur',
                    'destination' => $split['stripe_account_id'],
                    'transfer_group' => $transferGroup,
                    'metadata' => [
                        'order_id' => $order->id,
                        'seller_id' => $split['seller_id'],
                    ],
                ], [
                    // prevents duplicate payouts on webhook retries
                    'idempotency_key' => "order_{$order->id}_seller_{$split['seller_id']}",
                ]);

                $splits[$index]['transfer_id'] = $transfer->id;
            } catch (\Throwable $e) {
                Log::error("Transfer failed for order {$order->id}", [
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['status' => 'error'], 500);
            }
        }

        $order->update([
            'payment_reference' => $intent->latest_charge ?? null,
            'payment_intents' => $splits,
        ]);

        $orderService->markPaidAndFinalize($order);

        return response()->json(['status' => 'ok']);
    }
}
