<?php

namespace App\Repositories;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Collection;

class OrderRepository extends BaseRepository implements OrderRepositoryInterface
{
     public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return Order::with(['user', 'orderItem'])->get();
    }

    public function getById(int $id): ?Order
    {
        return Order::with(['user', 'orderItem'])->find($id);
    }
}
