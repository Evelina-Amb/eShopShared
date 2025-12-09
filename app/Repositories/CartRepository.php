<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Collection;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return Cart::with(['user', 'Listing'])->get();
    }

    public function getById(int $id): ?Cart
    {
        return Cart::with(['user', 'Listing'])->find($id);
    }

    public function getByUser(int $userId): Collection
    {
    return Cart::with(['user', 'Listing'])
        ->where('user_id', $userId)
        ->get();
    }
}
 