<?php

namespace App\Repositories;

use App\Models\ListingPhoto;
use App\Repositories\Contracts\ListingPhotoRepositoryInterface;
use Illuminate\Support\Collection;

class ListingPhotoRepository  extends BaseRepository implements ListingPhotoRepositoryInterface
{
     public function __construct(ListingPhoto $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return ListingPhoto::with('listing')->get();
    }

    public function getById(int $id): ?ListingPhoto
    {
        return ListingPhoto::with('listing')->find($id);
    }
}
