<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Models\Listing;
use App\Models\OrderItem;

class ReviewService
{
    protected ReviewRepositoryInterface $reviewRepository;

    public function __construct(ReviewRepositoryInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function getAll()
    {
        return $this->reviewRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->reviewRepository->getById($id);
    }

    public function create(array $data)
    {
        $userId    = $data['user_id'];
        $listingId = $data['listing_id'];

        $listing = Listing::find($listingId);
        if (!$listing) {
            throw new \Exception("Listing not found");
        }

        if (!$listing->is_renewable && $listing->kiekis < 1) {
            throw new \Exception("Reviews are only allowed for renewable listings or non-renewable listings with a higher quantity");
        }

        if ($listing->user_id == $userId) {
            throw new \Exception("You cannot review your own listing");
        }

        $existing = Review::where('listing_id', $listingId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            throw new \Exception("You have already reviewed this listing.");
        }

       $hasPurchased = OrderItem::where('listing_id', $listingId)
        ->whereHas('order', function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->where('statusas', \App\Models\Order::STATUS_PAID);
        })
    ->exists();

        if (!$hasPurchased) {
            throw new \Exception("You can only review listings you purchased");
        }

        return $this->reviewRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $review = $this->reviewRepository->getById($id);
        if (!$review) {
            return null;
        }

        return $this->reviewRepository->update($review, $data);
    }

    public function delete(int $id)
    {
        $review = $this->reviewRepository->getById($id);
        if (!$review) {
            return false;
        }

        return $this->reviewRepository->delete($review);
    }
}
