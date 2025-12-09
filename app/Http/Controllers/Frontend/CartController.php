<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class CartController extends Controller
{
    // Show current users cart
    public function index()
    {
        $cartItems = Cart::with('listing.photos')
            ->where('user_id', auth()->id())
            ->get();

        return view('frontend.cart', compact('cartItems'));
    }

    // Add a listing to cart
    public function add(Listing $listing, Request $request)
    {
        $userId = auth()->id();
        $quantity = (int) ($request->quantity ?? 1);

        // Check stock limits
        if ($listing->kiekis < $quantity) {
            return back()->with('error', "Only {$listing->kiekis} items available.");
        }

        $cartItem = Cart::where('user_id', $userId)
            ->where('listing_id', $listing->id)
            ->first();

        $newQty = $cartItem ? $cartItem->kiekis + $quantity : $quantity;

        if ($newQty > $listing->kiekis) {
            return back()->with('error', "You cannot add more than {$listing->kiekis} units of this item.");
        }

        if ($cartItem) {
            $cartItem->kiekis = $newQty;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => $userId,
                'listing_id' => $listing->id,
                'kiekis' => $quantity,
            ]);
        }

        session(['cart_count' => Cart::where('user_id', $userId)->count()]);

        return back()->with('success', 'Item added to cart');
    }

    // Increase quantity
    public function increase(Cart $cart)
    {
        $this->authorizeCart($cart);
        $listing = $cart->listing;

        if ($cart->kiekis + 1 > $listing->kiekis) {
            return back()->with('error', "Only {$listing->kiekis} units available.");
        }

        $cart->kiekis++;
        $cart->save();

        return back();
    }

    // Decrease quantity
    public function decrease(Cart $cart)
    {
        $this->authorizeCart($cart);

        if ($cart->kiekis > 1) {
            $cart->kiekis--;
            $cart->save();
        }

        return back();
    }

    // Remove an item completely
    public function remove(Cart $cart)
    {
        $this->authorizeCart($cart);

        $cart->delete();

        session(['cart_count' => Cart::where('user_id', auth()->id())->count()]);

        return back()->with('success', 'Item removed from cart.');
    }

    // Checkout logic
    public function checkout()
    {
        $userId = auth()->id();

        $cartItems = Cart::with('listing')->where('user_id', $userId)->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Your cart is empty.');
        }

        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            if ($item->listing) {
                $total += $item->listing->kaina * $item->kiekis;
            }
        }

        // Create order
        $order = Order::create([
            'user_id'      => $userId,
            'pirkimo_data' => Carbon::now(),
            'bendra_suma'  => $total,
            'statusas'     => 'completed',
        ]);

        // Process each cart item
        foreach ($cartItems as $item) {

            $listing = $item->listing;

            $listing->kiekis -= $item->kiekis;

        if ($listing->tipas === 'paslauga') {
            } else {
            // If sold out & NON-RENEWABLE hide
            if ($listing->kiekis <= 0 && $listing->is_renewable == 0) {
                $listing->statusas = 'parduotas';
                $listing->is_hidden = 1;
            }
        }

            $listing->save();

            // Create order item
            OrderItem::create([
                'order_id'   => $order->id,
                'listing_id' => $listing->id,
                'kaina'      => $listing->kaina,
                'kiekis'     => $item->kiekis,
            ]);
        }

        // Clear cart
        Cart::where('user_id', $userId)->delete();
        session(['cart_count' => 0]);

        return redirect('/')
            ->with('success', 'Purchase completed! Your order has been saved.');
    }

    private function authorizeCart(Cart $cart)
    {
        if ($cart->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
    }

    public function clearAll()
    {
        $userId = auth()->id();

        Cart::where('user_id', $userId)->delete();
        session(['cart_count' => 0]);

        return back()->with('success', 'Cart cleared successfully.');
    }
}
