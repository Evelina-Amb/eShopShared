<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('listing.photos')
            ->where('user_id', auth()->id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Cart is empty.');
        }

        $total = $cartItems->sum(fn ($i) => $i->listing->kaina * $i->kiekis);

        return view('frontend.checkout.index', compact('cartItems', 'total'));
    }

    public function pay(Request $request, OrderService $orderService)
    {
        $data = $request->validate([
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
        ]);

        $order = $orderService->createPendingFromCart(auth()->id(), $data);

        $order->load('orderItem.Listing.user');

        $groups = $order->orderItem->groupBy(fn ($item) => $item->Listing->user->id);

        Stripe::setApiKey(config('services.stripe.secret'));

        $platformPercent = 0.10;
        $smallOrderThreshold = 5.00;
        $smallOrderFee = 0.30;

        $paymentIntentsOut = [];

        $totalChargedCents = 0;
        $totalPlatformFeeCents = 0;
        $totalSmallOrderFeeCents = 0;

        foreach ($groups as $sellerId => $items) {
            $seller = $items->first()->Listing->user;

            if (!$seller->stripe_account_id || !$seller->stripe_onboarded) {
                return response()->json([
                    'error' => "Seller {$seller->id} is not ready to receive payments."
                ], 400);
            }

            $sellerSubtotal = (float) $items->sum(fn ($i) => $i->kaina * $i->kiekis);
            $sellerSubtotal = round($sellerSubtotal, 2);

            $platformFee = round($sellerSubtotal * $platformPercent, 2);
            $extraFee = $sellerSubtotal < $smallOrderThreshold ? $smallOrderFee : 0.00;

            $buyerPays = $sellerSubtotal + $extraFee;
            $sellerReceives = $sellerSubtotal - $platformFee;

            $buyerPaysCents = (int) round($buyerPays * 100);
            $platformFeeCents = (int) round($platformFee * 100);
            $extraFeeCents = (int) round($extraFee * 100);
            $sellerReceivesCents = (int) round($sellerReceives * 100);

            $intent = PaymentIntent::create([
                'amount' => $buyerPaysCents,
                'currency' => 'eur',
                'payment_method_types' => ['card'],

                'transfer_data' => [
                    'destination' => $seller->stripe_account_id,
                    'amount' => $sellerReceivesCents,
                ],

                'application_fee_amount' => $platformFeeCents + $extraFeeCents,

                'metadata' => [
                    'order_id' => $order->id,
                    'seller_id' => $seller->id,
                    'platform_fee_cents' => $platformFeeCents,
                    'small_order_fee_cents' => $extraFeeCents,
                ],
            ]);

            $paymentIntentsOut[] = [
                'seller_id' => $seller->id,
                'payment_intent_id' => $intent->id,
                'client_secret' => $intent->client_secret,
                'amount_cents' => $buyerPaysCents,
            ];

            $totalChargedCents += $buyerPaysCents;
            $totalPlatformFeeCents += $platformFeeCents;
            $totalSmallOrderFeeCents += $extraFeeCents;
        }

        $order->update([
            'payment_provider' => 'stripe',
            'payment_intents' => $paymentIntentsOut,
            'amount_charged_cents' => $totalChargedCents,
            'platform_fee_cents' => $totalPlatformFeeCents,
            'small_order_fee_cents' => $totalSmallOrderFeeCents,
        ]);

        return response()->json([
            'order_id' => $order->id,
            'payment_intents' => $paymentIntentsOut,
        ]);
    }

    public function success(Request $request, OrderService $orderService)
    {
        $orderId = $request->query('order_id');

        if (!$orderId) {
            return redirect()->route('cart.index')->with('error', 'Missing order reference.');
        }

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->route('cart.index')->with('error', 'Order not found.');
        }

        $intents = $order->payment_intents ?? [];
        if (count($intents) === 0) {
            return redirect()->route('checkout.index')->with('error', 'Missing payment intents.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        foreach ($intents as $pi) {
            $piId = $pi['payment_intent_id'] ?? null;
            if (!$piId) {
                return redirect()->route('checkout.index')->with('error', 'Invalid payment reference.');
            }

            $intent = PaymentIntent::retrieve($piId);
            if (($intent->status ?? null) !== 'succeeded') {
                return redirect()->route('checkout.index')->with('error', 'Payment not completed.');
            }
        }

        $orderService->markPaidAndFinalize($order);

        session(['cart_count' => 0]);

        return view('frontend.checkout.success');
    }
}
