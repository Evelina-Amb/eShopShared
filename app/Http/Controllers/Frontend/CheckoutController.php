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
            return redirect()->route('cart.index')
                ->with('error', 'Cart is empty.');
        }

        $total = $cartItems->sum(fn ($i) => $i->listing->kaina * $i->kiekis);

        return view('frontend.checkout.index', compact('cartItems', 'total'));
    }

    public function pay(Request $request, OrderService $orderService)
    {
        //  Validate address data
        $data = $request->validate([
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
        ]);

        //  Create pending order from cart
        $order = $orderService->createPendingFromCart(auth()->id(), $data);

        // Load order relations → seller
        $order->load('orderItem.Listing.user');

        $sellers = $order->orderItem
            ->pluck('Listing.user')
            ->unique('id');

        if ($sellers->count() !== 1) {
            return response()->json([
                'error' => 'Order must contain items from one seller only.'
            ], 400);
        }

        $seller = $sellers->first();

        //  Ensure seller is Stripe-ready
        if (
            !$seller->stripe_account_id ||
            !$seller->stripe_onboarded
        ) {
            return response()->json([
                'error' => 'Seller is not ready to receive payments.'
            ], 400);
        }

        // Create Stripe PaymentIntent (DESTINATION CHARGE)
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $intent = PaymentIntent::create([
                'amount' => (int) round($order->bendra_suma * 100),
                'currency' => 'eur',
                'payment_method_types' => ['card'],

                // SEND MONEY TO SELLER
                'transfer_data' => [
                    'destination' => $seller->stripe_account_id,
                ],

                // 'application_fee_amount' => (int) round($order->bendra_suma * 0.10 * 100),

                'metadata' => [
                    'order_id' => $order->id,
                    'seller_id' => $seller->id,
                ],
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {

            // LOG REAL STRIPE ERROR
            logger()->error('Stripe PaymentIntent creation failed', [
                'message' => $e->getMessage(),
                'stripe_code' => $e->getStripeCode(),
                'seller_account' => $seller->stripe_account_id,
                'order_id' => $order->id,
            ]);

            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }

        //  Save Stripe references
        $order->update([
            'payment_provider' => 'stripe',
            'payment_intent_id' => $intent->id,
        ]);

        //  Return client secret to frontend
        return response()->json([
            'client_secret' => $intent->client_secret,
        ]);
    }

    public function success(Request $request, OrderService $orderService)
    {
        $paymentIntentId = $request->query('payment_intent');

        if (!$paymentIntentId) {
            return redirect()->route('cart.index')
                ->with('error', 'Missing payment reference.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::retrieve($paymentIntentId);

        if (($intent->status ?? null) !== 'succeeded') {
            return redirect()->route('checkout.index')
                ->with('error', 'Payment not completed.');
        }

        $order = Order::where('payment_intent_id', $paymentIntentId)->first();

        if ($order) {
            $orderService->markPaidAndFinalize($order);
        }

        session(['cart_count' => 0]);

        return view('frontend.checkout.success');
    }
}
