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

        $groups = $order->orderItem->groupBy(fn ($item) => $item->Listing->user->id);

        $platformPercent = 0.10;
        $smallOrderThreshold = 5.00;
        $smallOrderFee = 0.30;

        $splits = [];
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

            $sellerSubtotalCents = (int) round($sellerSubtotal * 100);
            $platformFeeCents = (int) round($platformFee * 100);
            $extraFeeCents = (int) round($extraFee * 100);
            $buyerPaysCents = (int) round($buyerPays * 100);
            $sellerReceivesCents = (int) round($sellerReceives * 100);

            $splits[] = [
                'seller_id' => (int) $seller->id,
                'stripe_account_id' => (string) $seller->stripe_account_id,
                'seller_subtotal_cents' => $sellerSubtotalCents,
                'platform_fee_cents' => $platformFeeCents,
                'small_order_fee_cents' => $extraFeeCents,
                'seller_amount_cents' => $sellerReceivesCents,
                'transfer_id' => null,
            ];

            $totalChargedCents += $buyerPaysCents;
            $totalPlatformFeeCents += $platformFeeCents;
            $totalSmallOrderFeeCents += $extraFeeCents;
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => $totalChargedCents,
            'currency' => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => [
                'order_id' => (string) $order->id,
            ],
        ]);

        $order->update([
            'payment_provider' => 'stripe',
            'payment_intent_id' => $intent->id,
            'payment_intents' => $splits,
            'amount_charged_cents' => $totalChargedCents,
            'platform_fee_cents' => $totalPlatformFeeCents,
            'small_order_fee_cents' => $totalSmallOrderFeeCents,
        ]);

       return response()->json([
    'order_id' => $order->id,
    'client_secret' => $intent->client_secret,

    'breakdown' => [
        'items_total_cents' => (int) round($order->bendra_suma * 100),
        'small_order_fee_cents' => $totalSmallOrderFeeCents,
        'total_cents' => $totalChargedCents,
    ],
]);
    }
        
    public function shipping(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|integer',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
        ]);

        $order = Order::where('id', $data['order_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->statusas !== Order::STATUS_PENDING) {
            return response()->json(['error' => 'Order is not pending.'], 400);
        }

        $order->update([
            'shipping_address' => [
                'address' => $data['address'],
                'city' => $data['city'],
                'postal_code' => $data['postal_code'],
                'country' => $data['country'],
            ],
        ]);

        return response()->json(['ok' => true]);
    }

    public function success(Request $request)
    {
        return view('frontend.checkout.success');
    }
}
