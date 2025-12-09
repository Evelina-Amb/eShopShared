<?php

namespace App\Repositories;

use App\Models\OrderItem;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use Illuminate\Support\Collection;

class OrderItemRepository extends BaseRepository implements OrderItemRepositoryInterface
{
    public function __construct(OrderItem $model)
    {
        parent::__construct($model);
    }

    public function getAll(): Collection
    {
        return OrderItem::with(['order', 'listing'])->get();
    }

    public function getById(int $id): ?OrderItem
    {
        return OrderItem::with(['order', 'listing'])->find($id);
    }

}
 