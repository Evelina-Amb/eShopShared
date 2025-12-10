<?php

namespace App\Services;

use App\Models\Listing;
use App\Repositories\Contracts\ListingRepositoryInterface;

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

        // SERVICES: can always be deleted
        if ($listing->tipas === 'paslauga') {
            return (bool) $this->listingRepository->delete($listing);
        }

        // PRODUCTS:

        // Criterion: "has ever been sold"
        // We prefer to use order items if relation exists.
        $hasOrders = false;

        if (method_exists($listing, 'orderItems')) {
            $hasOrders = $listing->orderItems()->exists();
        }

        // Fallback: use status field if present
        if ($listing->statusas === 'parduotas') {
            $hasOrders = true;
        }

        // If sold at least once → hide
        if ($hasOrders) {
            $listing->is_hidden = true;
            $listing->save();

            return true;
        }

        // Never sold → hard delete
        return (bool) $this->listingRepository->delete($listing);
    }

}
