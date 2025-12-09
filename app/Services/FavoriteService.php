<?php

namespace App\Services;

use App\Models\Favorite;
use App\Repositories\Contracts\FavoriteRepositoryInterface;

class FavoriteService
{
    protected FavoriteRepositoryInterface $favoriteRepository;

    public function __construct(FavoriteRepositoryInterface $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    public function getAll()
    {
        return $this->favoriteRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->favoriteRepository->getById($id);
    }

    public function create(array $data)
{
    $userId = $data['user_id'];
    $listingId = $data['listing_id'];

    $listing = \App\Models\Listing::find($listingId);
    if (!$listing) {
        throw new \Exception("Listing not found");
    }

    //Prevent duplicates
    $exists = \App\Models\Favorite::where('user_id', $userId)
                ->where('listing_id', $listingId)
                ->exists();

    if ($exists) {
        throw new \Exception("This listing is already in your favorites.");
    }

    return $this->favoriteRepository->create($data);
}

    public function update(int $id, array $data)
    {
        $favorite = $this->favoriteRepository->getById($id);
        if (!$favorite) return null;

        return $this->favoriteRepository->update($favorite, $data);
    }

    public function delete(int $id)
    {
        $favorite = $this->favoriteRepository->getById($id);
        if (!$favorite) return false;

        return $this->favoriteRepository->delete($favorite);
    }
}
