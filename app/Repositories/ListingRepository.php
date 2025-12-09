<?php

namespace App\Repositories;

use App\Models\Listing;
use App\Repositories\Contracts\ListingRepositoryInterface;
use Illuminate\Support\Collection;

class ListingRepository extends BaseRepository implements ListingRepositoryInterface
{
    public function __construct(Listing $model)
    {
        parent::__construct($model);
    }

    public function getPublic(): Collection
    {
        return Listing::where('is_hidden', false)
            ->where('statusas', '!=', 'parduotas')
            ->with(['user', 'category', 'photos'])
            ->get();
    }

    public function getByUser(int $userId): Collection
    {
        return Listing::where('user_id', $userId)
            ->where('is_hidden', false)
            ->with(['category', 'photos'])
            ->get();
    }

    public function search(array $filters): Collection
    {
        $query = Listing::where('is_hidden', false)
            ->where('statusas', '!=', 'parduotas')
            ->with([
                'user',
                'category',
                'photos',
                'user.address.city',
                'review.user'
            ]);

        // Keyword search
        if (!empty($filters['q'])) {
            $q = $filters['q'];

            $query->where(function ($q2) use ($q) {
                $q2->where('pavadinimas', 'LIKE', "%{$q}%")
                    ->orWhere('aprasymas', 'LIKE', "%{$q}%");
            });
        }

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);

        }

        // Type filter (preke / paslauga)
        if (!empty($filters['tipas'])) {
            $query->where('tipas', $filters['tipas']);
        }

        // Price range
        if (!empty($filters['min_price'])) {
            $query->where('kaina', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('kaina', '<=', $filters['max_price']);
        }

        // City filter
        if (!empty($filters['city_id'])) {
            $query->whereHas('user.address', function ($q) use ($filters) {
                $q->where('city_id', $filters['city_id']);
            });
        }

        return $query->get();
    }

    public function getByIds(array $ids): Collection
    {
        return Listing::where('is_hidden', false)
            ->whereIn('id', $ids)
            ->with(['photos', 'category', 'user'])
            ->get();
    }
}
