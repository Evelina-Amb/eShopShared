<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            Log::warning('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if (($event->type ?? null) !== 'payment_intent.succeeded') {
            return response()->json(['status' => 'ignored']);
        }

        $intent = $event->data->object;

        return DB::transaction(function () use ($intent, $orderService) {

            $order = Order::with('orderItem.Listing.user')
                ->where('payment_intent_id', $intent->id)
                ->lockForUpdate()
                ->first();

            if (!$order) {
                Log::warning('Stripe webhook: order not found', [
                    'payment_intent' => $intent->id,
                ]);
                return response()->json(['status' => 'ok']);
            }

            if ($order->statusas === Order::STATUS_PAID) {
                return response()->json(['status' => 'ok']);
            }

            $splits = $order->payment_intents ?? [];
            if (!is_array($splits) || empty($splits)) {
                Log::error('Stripe webhook: missing payment split data', [
                    'order_id' => $order->id,
                ]);
                return response()->json(['error' => 'Missing split data'], 500);
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
                            'order_id'  => $order->id,
                            'seller_id' => $split['seller_id'],
                        ],
                    ], [
                        'idempotency_key' => "order_{$order->id}_seller_{$split['seller_id']}",
                    ]);

                    $splits[$index]['transfer_id'] = $transfer->id;

                } catch (\Throwable $e) {
                    Log::error('Stripe transfer failed', [
                        'order_id' => $order->id,
                        'seller_id' => $split['seller_id'] ?? null,
                        'error' => $e->getMessage(),
                    ]);
                    return response()->json(['error' => 'Transfer failed'], 500);
                }
            }

            $order->update([
                'payment_reference' => $intent->latest_charge ?? null,
                'payment_intents'   => $splits,
            ]);

            $orderService->markPaidAndFinalize($order);
\Mail::to($order->user->el_pastas)
    ->queue(new \App\Mail\BuyerOrderPlacedMail($order));
            
            return response()->json(['status' => 'ok']);
        });
    }
}
