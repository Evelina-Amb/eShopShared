<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request, OrderService $orderService)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        $secret = config('services.stripe.webhook_secret');

        try {
            if ($secret) {
                $event = Webhook::constructEvent($payload, $sigHeader, $secret);
            } else {
                $event = json_decode($payload);
            }
        } catch (\Throwable $e) {
            Log::warning('Stripe webhook signature verification failed: '.$e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;

            $order = Order::where('payment_intent_id', $intent->id)->first();

            if (!$order) {
                Log::warning("Stripe webhook: order not found for intent {$intent->id}");
                return response()->json(['status' => 'ok']);
            }

            if ($order->statusas === Order::STATUS_PAID) {
                return response()->json(['status' => 'ok']);
            }

           $orderService->markPaidAndFinalize($order);

            return response()->json(['status' => 'ok']);
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $intent = $event->data->object;

            $order = Order::where('payment_intent_id', $intent->id)->first();
            if ($order && $order->statusas !== Order::STATUS_PAID) {
                $order->update([
                    'statusas' => Order::STATUS_FAILED,
                ]);
            }

            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'ignored']);
    }
}
