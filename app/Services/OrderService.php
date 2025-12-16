<?php

namespace App\Services;

use App\Models\OrderItem; 
use App\Models\Listing;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createPendingFromCart(int $userId, array $shippingAddress): Order
    {
        return DB::transaction(function () use ($userId, $shippingAddress) {

            $cartItems = Cart::with('listing')
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \RuntimeException('Cart empty.');
            }

            $total = 0;

            foreach ($cartItems as $item) {
                if ($item->listing->tipas !== 'paslauga' &&
                    $item->kiekis > $item->listing->kiekis) {
                    throw new \RuntimeException('Not enough stock.');
                }

                $total += $item->listing->kaina * $item->kiekis;
            }

            $order = Order::create([
                'user_id' => $userId,
                'pirkimo_data' => now(),
                'bendra_suma' => $total,
                'statusas' => Order::STATUS_PENDING,
                'shipping_address' => $shippingAddress,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'listing_id' => $item->listing_id,
                    'kaina' => $item->listing->kaina,
                    'kiekis' => $item->kiekis,
                ]);
            }

            return $order;
        });
    }

   public function markPaidAndFinalize(Order $order): void
{
    DB::transaction(function () use ($order) {

        // Reload and lock the order row to prevent race conditions
        $order = Order::where('id', $order->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($order->statusas === Order::STATUS_PAID) {
            return;
        }

        // Mark order as paid ONLY once
        $order->update([
            'statusas' => Order::STATUS_PAID,
        ]);

        // Finalize order items
        foreach ($order->OrderItem as $item) {
            $listing = Listing::where('id', $item->listing_id)
                ->lockForUpdate()
                ->first();

            if (!$listing) {
                continue;
            }

            // Services do not reduce stock
            if ($listing->tipas === 'paslauga') {
                continue;
            }

            $listing->kiekis -= (int) $item->kiekis;

            // Hide non-renewable listings when sold out
            if ($listing->kiekis <= 0 && (int) $listing->is_renewable === 0) {
                $listing->statusas = 'parduotas';
                $listing->is_hidden = 1;
            }

            $listing->save();
        }

        // Clear user's cart AFTER successful payment
        Cart::where('user_id', $order->user_id)->delete();
    });
}

    
}
