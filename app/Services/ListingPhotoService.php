<?php

namespace App\Services;

use App\Models\ListingPhoto;
use App\Repositories\Contracts\ListingPhotoRepositoryInterface;

class ListingPhotoService
{
    protected ListingPhotoRepositoryInterface $listingPhotoRepository;

    public function __construct(ListingPhotoRepositoryInterface $listingPhotoRepository)
    {
        $this->listingPhotoRepository = $listingPhotoRepository;
    }

    public function getAll()
    {
        return $this->listingPhotoRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->listingPhotoRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->listingPhotoRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $photo = $this->listingPhotoRepository->getById($id);
        if (!$photo) return null;

        return $this->listingPhotoRepository->update($photo, $data);
    }

    public function delete(int $id)
    {
        $photo = $this->listingPhotoRepository->getById($id);
        if (!$photo) return false;

        return $this->listingPhotoRepository->delete($photo);
    }
}
