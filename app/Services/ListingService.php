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
    return $this->listingRepository->search([
        'sort' => request('sort')
    ]);
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
        if (empty($data['statusas'])) {
            $data['statusas'] = 'aktyvus';
        }
            if (($data['tipas'] ?? null) === 'paslauga') {
                $data['package_size'] = 'S';
                $data['kiekis'] = 1;
                $data['is_renewable'] = false;
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

        // Prevent service listings from ever becoming sold
        if (
            $listing->tipas === 'paslauga' &&
            isset($data['statusas']) &&
            $data['statusas'] === 'parduotas'
        ) {
            throw new \Exception('Services cannot be marked as sold.');
        }

        $allowedFields = [
            'pavadinimas',
            'aprasymas',
            'kaina',
            'tipas',
            'category_id',
            'kiekis',
            'is_renewable',
            'package_size', 
        ];

        $updateData = array_intersect_key($data, array_flip($allowedFields));

        return $this->listingRepository->update($listing, $updateData);
    }

  public function delete(Listing $listing): string
{
    if ($listing->orderItems()->exists()) {
        $listing->is_hidden = true;
        $listing->save();

        return 'hidden';
    }

    $listing->delete();

    return 'deleted';
}


}
