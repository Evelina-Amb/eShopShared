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

        $order = Order::with('orderItem')
            ->lockForUpdate()
            ->findOrFail($order->id);


        $order->update([
            'statusas' => Order::STATUS_PAID,
        ]);

        foreach ($order->orderItem as $item) {
            $listing = Listing::lockForUpdate()->find($item->listing_id);

            if (!$listing) {
                continue;
            }

            if ($listing->tipas === 'paslauga') {
                continue;
            }

            $listing->kiekis -= (int) $item->kiekis;

            if ($listing->kiekis <= 0 && (int) $listing->is_renewable === 0) {
                $listing->statusas = 'parduotas';
                $listing->is_hidden = 1;
            }

            $listing->save();
        }
        Cart::where('user_id', $order->user_id)->delete();
    });
}


    
}
