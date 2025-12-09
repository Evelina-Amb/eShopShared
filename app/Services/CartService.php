<?php

namespace App\Services;

use App\Models\Cart;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartService
{
    protected CartRepositoryInterface $cartRepository;

    public function __construct(CartRepositoryInterface $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getAll()
    {
        return $this->cartRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->cartRepository->getById($id);
    }

    public function clearItem(int $userId, int $listingId)
{
    return \App\Models\Cart::where('user_id', $userId)
                           ->where('listing_id', $listingId)
                           ->delete();
}

public function clearAll(int $userId)
{
    return \App\Models\Cart::where('user_id', $userId)->delete();
}

    public function create(array $data)
{
    $userId = $data['user_id'];
    $listingId = $data['listing_id'];

    $listing = \App\Models\Listing::find($listingId);

    if (!$listing) {
        throw new \Exception("Listing not found");
    }

    //User cannot add their own listing
    if ($listing->user_id == $userId) {
        throw new \Exception("You cannot add your own listing to cart");
    }

    //Cannot add sold or reserved listing
    if ($listing->statusas === 'parduotas') {
        throw new \Exception("Listing is already sold");
    }

    if ($listing->statusas === 'rezervuotas') {
        throw new \Exception("Listing is reserved and cannot be added");
    }

    //Prevent duplicates
    $exists = Cart::where('user_id', $userId)
                  ->where('listing_id', $listingId)
                  ->first();

    if ($exists) {
        throw new \Exception("Listing is already in your cart");
    }

    return $this->cartRepository->create($data);
}


    public function update(int $id, array $data)
    {
        $cart = $this->cartRepository->getById($id);
        if (!$cart) return null;

        return $this->cartRepository->update($cart, $data);
    }

    public function delete(int $id)
    {
        $cart = $this->cartRepository->getById($id);
        if (!$cart) return false;

        return $this->cartRepository->delete($cart);
    }
}
