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

    public function intent(OrderService $orderService)
    {
        $placeholder = [
            'address' => '__pending__',
            'city' => '__pending__',
            'postal_code' => '__pending__',
            'country' => '__pending__',
        ];

        $order = $orderService->createPendingFromCart(auth()->id(), $placeholder);
        $order->load('orderItem.Listing.user');

        Stripe::setApiKey(config('services.stripe.secret'));

        $amountCents = (int) round($order->bendra_suma * 100);

        $intent = PaymentIntent::create([
            'amount' => $amountCents,
            'currency' => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'order_id' => $order->id,
            ],
        ]);

        $order->update([
            'payment_provider' => 'stripe',
            'payment_intent_id' => $intent->id,
            'amount_charged_cents' => $amountCents,
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
}
