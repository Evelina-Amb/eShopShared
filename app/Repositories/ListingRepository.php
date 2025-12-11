<?php

namespace App\Repositories;

use App\Models\Listing;
use App\Repositories\Contracts\ListingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

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
            ->withCount([
                'favorites as is_favorited' => function ($q) {
                    if (Auth::check()) {
                        $q->where('user_id', Auth::id());
                    } else {
                        $q->whereRaw('0 = 1');
                    }
                }
            ])
            ->get();
    }

    public function getByUser(int $userId): Collection
    {
        return Listing::where('user_id', $userId)
            ->where('is_hidden', false)
            ->with(['category', 'photos'])
            ->withCount([
                'favorites as is_favorited' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ])
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
            ])
            ->withCount([
                'favorites as is_favorited' => function ($q) {
                    if (Auth::check()) {
                        $q->where('user_id', Auth::id());
                    } else {
                        $q->whereRaw('0 = 1');
                    }
                }
            ]);

        if (!empty($filters['q'])) {
            $keyword = $filters['q'];
            $query->where(function ($q2) use ($keyword) {
                $q2->where('pavadinimas', 'LIKE', "%{$keyword}%")
                   ->orWhere('aprasymas', 'LIKE', "%{$keyword}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['tipas'])) {
            $query->where('tipas', $filters['tipas']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('kaina', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('kaina', '<=', $filters['max_price']);
        }

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
            ->withCount([
                'favorites as is_favorited' => function ($q) {
                    if (Auth::check()) {
                        $q->where('user_id', Auth::id());
                    } else {
                        $q->whereRaw('0 = 1');
                    }
                }
            ])
            ->get();
    }

   public function delete($listing)
    {
        $listing->is_hidden = true;
        return $listing->save();
    }

}
