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
}
