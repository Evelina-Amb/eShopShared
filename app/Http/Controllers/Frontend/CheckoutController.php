<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Cart;
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

    public function intent()
{
    $cartItems = Cart::where('user_id', auth()->id())->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['error' => 'Cart is empty'], 400);
    }

    $total = $cartItems->sum(fn ($i) => $i->listing->kaina * $i->kiekis);

    Stripe::setApiKey(config('services.stripe.secret'));

    $intent = PaymentIntent::create([
        'amount' => (int) round($total * 100),
        'currency' => 'eur',
        'automatic_payment_methods' => ['enabled' => true],
    ]);

    return response()->json([
        'client_secret' => $intent->client_secret,
    ]);
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

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => (int) round($order->bendra_suma * 100),
            'currency' => 'eur',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata' => ['order_id' => $order->id],
        ]);

        $order->update([
            'payment_provider' => 'stripe',
            'payment_intent_id' => $intent->id,
        ]);

        return response()->json(['client_secret' => $intent->client_secret]);
    }

    public function success()
    {
        return view('frontend.checkout.success');
    }
}
