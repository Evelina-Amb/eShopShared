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

        Stripe::setApiKey(config('services.stripe.secret'));

        $platformPercent = 0.10;
        $smallOrderThreshold = 5.00;
        $smallOrderFee = 0.30;

        $splits = [];
        $totalCharged = 0;
        $platformFeeTotal = 0;

        foreach ($order->orderItem->groupBy(fn ($i) => $i->Listing->user->id) as $sellerId => $items) {
            $seller = $items->first()->Listing->user;

            if (!$seller->stripe_account_id || !$seller->stripe_onboarded) {
                return response()->json([
                    'error' => "Seller {$seller->id} is not ready to receive payments."
                ], 400);
            }

            $subtotal = round($items->sum(fn ($i) => $i->kaina * $i->kiekis), 2);
            $platformFee = round($subtotal * $platformPercent, 2);
            $extraFee = $subtotal < $smallOrderThreshold ? $smallOrderFee : 0.00;

            $buyerPays = $subtotal + $extraFee;
            $sellerReceives = $subtotal - $platformFee;

            $splits[] = [
                'seller_id' => $seller->id,
                'stripe_account_id' => $seller->stripe_account_id,
                'seller_amount_cents' => (int) round($sellerReceives * 100),
            ];

            $totalCharged += (int) round($buyerPays * 100);
            $platformFeeTotal += (int) round(($platformFee + $extraFee) * 100);
        }

        $intent = PaymentIntent::create([
            'amount' => $totalCharged,
            'currency' => 'eur',
            'payment_method_types' => ['card'],
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        $order->update([
            'payment_provider' => 'stripe',
            'payment_intent_id' => $intent->id,
            'payment_intents' => $splits,
            'amount_charged_cents' => $totalCharged,
            'platform_fee_cents' => $platformFeeTotal,
        ]);

        return response()->json([
            'client_secret' => $intent->client_secret,
        ]);
    }

    public function success(Request $request, OrderService $orderService)
    {
        $paymentIntentId = $request->query('payment_intent');

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::retrieve($paymentIntentId);
        if ($intent->status !== 'succeeded') {
            return redirect()->route('checkout.index')->with('error', 'Payment not completed.');
        }

        $order = Order::where('payment_intent_id', $paymentIntentId)->firstOrFail();

        foreach ($order->payment_intents as $split) {
            \Stripe\Transfer::create([
                'amount' => $split['seller_amount_cents'],
                'currency' => 'eur',
                'destination' => $split['stripe_account_id'],
                'transfer_group' => 'order_' . $order->id,
            ]);
        }

        $orderService->markPaidAndFinalize($order);
        session(['cart_count' => 0]);

        return view('frontend.checkout.success');
    }
}
