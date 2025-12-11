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

     public function delete($photo)
    {
        $listing = $photo->listing;

        // If this is the last photo → do NOT delete
        if ($listing->photos()->count() <= 1) {
            return 'last-photo';
        }

        // Remove file from storage
        if ($photo->failo_url) {
            Storage::delete('public/' . $photo->failo_url);
        }

        // Delete DB record
        return $photo->delete();
    }
}
