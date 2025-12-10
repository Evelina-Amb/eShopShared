<?php

namespace App\Services;

use App\Models\Listing;
use App\Repositories\Contracts\ListingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ListingService
{
    protected ListingRepositoryInterface $listingRepository;

    public function __construct(ListingRepositoryInterface $listingRepository)
    {
        $this->listingRepository = $listingRepository;
    }

    public function getAll()
    {
        return $this->listingRepository->getPublic();
    }

    public function getMine(int $userId)
    {
        return $this->listingRepository->getByUser($userId);
    }

    public function getById(int $id)
    {
        return $this->listingRepository->getById($id);
    }

    public function getByIds(array $ids)
    {
        return $this->listingRepository->getByIds($ids);
    }

    public function create(array $data)
    {
        // Default status
        if (empty($data['statusas'])) {
            $data['statusas'] = 'aktyvus';
        }

        return $this->listingRepository->create($data);
    }

    public function search(array $filters)
    {
        return $this->listingRepository->search($filters);
    }

    public function update(int $id, array $data)
    {
        $listing = $this->listingRepository->getById($id);

        if (!$listing) {
            return null;
        }

        // Prevent editing sold listings
        if ($listing->statusas === 'parduotas') {
            throw new \Exception('Negalima redaguoti parduoto skelbimo.');
        }

        // Prevent service listings from ever becoming "parduotas"
        if (
            $listing->tipas === 'paslauga' &&
            isset($data['statusas']) &&
            $data['statusas'] === 'parduotas'
        ) {
            throw new \Exception('Services cannot be marked as sold.');
        }

        // Ensure quantity + renewable flag update
        $allowedFields = [
            'pavadinimas',
            'aprasymas',
            'kaina',
            'tipas',
            'category_id',
            'kiekis',
            'is_renewable',
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));

        return $this->listingRepository->update($listing, $updateData);
    }

public function delete(int $id): bool
{
    $listing = $this->listingRepository->getById($id);

    if (!$listing) {
        return false;
    }

    // SERVICES → always hard delete
    if ($listing->tipas === 'paslauga') {
        return $this->forceDeleteListing($listing);
    }

    // PRODUCTS → hide if sold
    $hasOrders = false;

    if (method_exists($listing, 'orderItems')) {
        $hasOrders = $listing->orderItems()->exists();
    }

    if ($listing->statusas === 'parduotas') {
        $hasOrders = true;
    }

    if ($hasOrders) {
        $listing->is_hidden = true;
        $listing->save();
        return true;
    }

    // Never sold → hard delete
    return $this->forceDeleteListing($listing);
}

protected function forceDeleteListing(Listing $listing): bool
{
    return DB::transaction(function () use ($listing) {

        if (method_exists($listing, 'favoritedBy')) {
            $listing->favoritedBy()->detach();
        }

        if (method_exists($listing, 'photos')) {
            $listing->photos()->delete();
        }

        return (bool) $this->listingRepository->delete($listing);
    });
}

}
