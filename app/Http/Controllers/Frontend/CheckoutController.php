<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Transfer;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('listing.photos', 'listing.user')
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

        $amountCents = (int) round($order->bendra_suma * 100);
        $platformFeeCents = (int) round($order->bendra_suma * 0.10 * 100);

        $intent = PaymentIntent::create([
            'amount' => $amountCents,
            'currency' => 'eur',
            'payment_method_types' => ['card'],
            'application_fee_amount' => $platformFeeCents,
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        $order->update([
            'payment_provider' => 'stripe',
            'payment_intent_id' => $intent->id,
            'amount_charged_cents' => $amountCents,
            'platform_fee_cents' => $platformFeeCents,
        ]);

        return response()->json([
            'order_id' => $order->id,
            'client_secret' => $intent->client_secret,
        ]);
    }

    public function success(Request $request, OrderService $orderService)
    {
        $orderId = $request->query('order_id');
        if (!$orderId) {
            return redirect()->route('cart.index')->with('error', 'Missing order reference.');
        }

        $order = Order::with('orderItem.Listing.user')->findOrFail($orderId);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::retrieve($order->payment_intent_id);
        if ($intent->status !== 'succeeded') {
            return redirect()->route('checkout.index')->with('error', 'Payment not completed.');
        }

        // Split money to sellers
        $groups = $order->orderItem->groupBy(fn ($i) => $i->Listing->user->id);

        foreach ($groups as $items) {
            $seller = $items->first()->Listing->user;

            if (!$seller->stripe_account_id || !$seller->stripe_onboarded) {
                continue;
            }

            $subtotal = $items->sum(fn ($i) => $i->kaina * $i->kiekis);
            $sellerReceivesCents = (int) round($subtotal * 0.90 * 100);

            Transfer::create([
                'amount' => $sellerReceivesCents,
                'currency' => 'eur',
                'destination' => $seller->stripe_account_id,
                'transfer_group' => 'order_' . $order->id,
            ]);
        }

        $orderService->markPaidAndFinalize($order);
        session(['cart_count' => 0]);

        return view('frontend.checkout.success');
    }

    public function intent(OrderService $orderService)
{
    $order = $orderService->createPendingFromCart(auth()->id(), []);

    Stripe::setApiKey(config('services.stripe.secret'));

    $amountCents = (int) round($order->bendra_suma * 100);
    $platformFeeCents = (int) round($order->bendra_suma * 0.10 * 100);

    $intent = PaymentIntent::create([
        'amount' => $amountCents,
        'currency' => 'eur',
        'payment_method_types' => ['card'],
        'application_fee_amount' => $platformFeeCents,
        'metadata' => [
            'order_id' => $order->id,
        ],
    ]);

    $order->update([
        'payment_provider' => 'stripe',
        'payment_intent_id' => $intent->id,
        'amount_charged_cents' => $amountCents,
        'platform_fee_cents' => $platformFeeCents,
    ]);

    return response()->json([
        'order_id' => $order->id,
        'client_secret' => $intent->client_secret,
    ]);
}

}
