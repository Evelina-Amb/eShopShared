<?php

namespace App\Repositories;

use App\Models\Favorite;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use Illuminate\Support\Collection;

class FavoriteRepository extends BaseRepository implements FavoriteRepositoryInterface
{
    public function __construct(Favorite $model)
    {
        parent::__construct($model);
    }
    
    public function getAll(): Collection
    {
        return Favorite::where('user_id', auth()->id())
            ->with([
                'listing.photos',
                'listing.category',
                'listing.user',
            ])
            ->get();
    }

    public function getById(int $id): ?Favorite
    {
        return Favorite::where('user_id', auth()->id())
            ->with([
                'listing.photos',
                'listing.category',
                'listing.user',
            ])
            ->find($id);
    }

} 
