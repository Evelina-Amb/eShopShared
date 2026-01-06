<?php

namespace App\Services;

use App\Models\OrderItem; 
use App\Models\Listing;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderShipment;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendSellerNewOrderEmail;

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
         foreach ($order->payment_intents as $split) {
            OrderShipment::create([
                'order_id'     => $order->id,
                'seller_id'    => $split['seller_id'],
                'carrier'      => $order->shipping_address['carrier'] ?? 'omniva',
                'package_size' => $split['package_size'] ?? 'S',
                'price_cents'  => $split['shipping_cents'] ?? 0,
                'status'       => 'pending',
            ]);
        }
        Cart::where('user_id', $order->user_id)->delete();
    });

    $order = Order::with('orderItem.listing.user')->findOrFail($order->id);
    $groups = $order->orderItem->groupBy(fn ($i) => $i->listing->user_id);

foreach ($groups as $sellerId => $items) {
    SendSellerNewOrderEmail::dispatch($order->id, (int)$sellerId);
    }
}

    
}
