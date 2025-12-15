<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderService
{
    protected OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getAll()
    {
        return $this->orderRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->orderRepository->getById($id);
    }

    public function create(array $data)
{
    $userId = $data['user_id'];
    $items = $data['items'];

    $total = 0;
    $listingsData = [];

    foreach ($items as $item) {
        $listingId = $item['listing_id'];
        $listing = \App\Models\Listing::find($listingId);

        if (!$listing) {
            throw new \Exception("Listing not found: ID {$listingId}");
        }

        //Cannot buy own listing
        if ($listing->user_id == $userId) {
            throw new \Exception("You cannot buy your own listing: {$listing->pavadinimas}");
        }

        //Listing must be active
        if ($listing->statusas === 'parduotas') {
            throw new \Exception("Listing already sold: {$listing->pavadinimas}");
        }

        if ($listing->statusas === 'rezervuotas') {
            throw new \Exception("Listing is reserved: {$listing->pavadinimas}");
        }

        //Add to total price
        $price = $listing->kaina * $item['kiekis'];
        $total += $price;

        //Store for creating OrderItems later
        $listingsData[] = [
            'model'  => $listing,
            'kaina'  => $listing->kaina,
            'kiekis' => $item['kiekis']
        ];
    }

    //Create the main Order
    $order = $this->orderRepository->create([
        'user_id'     => $userId,
        'pirkimo_data'=> now(),
        'bendra_suma' => $total,
        'statusas'    => 'completed'
    ]);

    //Create OrderItems & update listings
    foreach ($listingsData as $itemData) {

        //Create OrderItem
        \App\Models\OrderItem::create([
            'order_id'    => $order->id,
            'listing_id'  => $itemData['model']->id,
            'kaina'       => $itemData['kaina'],
            'kiekis'      => $itemData['kiekis']
        ]);

        //Mark listing as sold
        $itemData['model']->update([
            'statusas' => 'parduotas'
        ]);
    }
//Clear user's cart
\App\Models\Cart::where('user_id', $userId)->delete();
    return $order->load(['orderItem', 'user']);
}

    public function update(int $id, array $data)
    {
        $order = $this->orderRepository->getById($id);
        if (!$order) return null;

        return $this->orderRepository->update($order, $data);
    }

    public function delete(int $id)
    {
        $order = $this->orderRepository->getById($id);
        if (!$order) return false;

        return $this->orderRepository->delete($order);
    }

    public function createPendingFromCart(int $userId, array $shippingAddress): Order
    {
        return DB::transaction(function () use ($userId, $shippingAddress) {

            $cartItems = Cart::with('listing')
                ->where('user_id', $userId)
                ->lockForUpdate()
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \RuntimeException('Your cart is empty.');
            }

            // Validate listings + stock again inside transaction
            $total = 0.0;

            foreach ($cartItems as $item) {
                if (!$item->listing) {
                    throw new \RuntimeException('One of the cart items is invalid.');
                }

                $listing = Listing::where('id', $item->listing_id)->lockForUpdate()->first();
                if (!$listing) {
                    throw new \RuntimeException('Listing not found.');
                }

                // If it’s a product, enforce stock. If service, skip stock constraint.
                if ($listing->tipas !== 'paslauga') {
                    if ($item->kiekis > $listing->kiekis) {
                        throw new \RuntimeException("Only {$listing->kiekis} units available for {$listing->pavadinimas}.");
                    }
                }

                $total += ((float) $listing->kaina) * (int) $item->kiekis;
            }

            $order = Order::create([
                'user_id' => $userId,
                'pirkimo_data' => now(),
                'bendra_suma' => $total,
                'statusas' => Order::STATUS_PENDING,
                'shipping_address' => $shippingAddress,
            ]);

            // Snapshot items
            foreach ($cartItems as $item) {
                $listing = $item->listing;

                OrderItem::create([
                    'order_id' => $order->id,
                    'listing_id' => $listing->id,
                    'kaina' => $listing->kaina,
                    'kiekis' => $item->kiekis,
                ]);
            }

            return $order;
        });
    }

public function markPaidAndFinalize(Order $order, array $paymentMeta = []): void
    {
        DB::transaction(function () use ($order, $paymentMeta) {

            $order = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($order->statusas === Order::STATUS_PAID) {
                return; 
            }

            $order->update([
                'statusas' => Order::STATUS_PAID,
                'payment_reference' => $paymentMeta['payment_reference'] ?? $order->payment_reference,
            ]);

            $this->finalizePaidOrder($order);

            // Clear cart after success
            Cart::where('user_id', $order->user_id)->delete();
        });
    }

public function finalizePaidOrder(Order $order): void
    {
        $items = OrderItem::where('order_id', $order->id)->get();

        foreach ($items as $item) {
            $listing = Listing::where('id', $item->listing_id)->lockForUpdate()->first();

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
    }

    
}
