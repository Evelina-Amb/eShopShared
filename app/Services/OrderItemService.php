<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Repositories\Contracts\OrderItemRepositoryInterface;

class OrderItemService
{
    protected OrderItemRepositoryInterface $orderItemRepository;

    public function __construct(OrderItemRepositoryInterface $orderItemRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
    }

    public function getAll()
    {
        return $this->orderItemRepository->getAll();
    }

    public function getById(int $id)
    {
        return $this->orderItemRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->orderItemRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $item = $this->orderItemRepository->getById($id);
        if (!$item) return null;

        return $this->orderItemRepository->update($item, $data);
    }

    public function delete(int $id)
    {
        $item = $this->orderItemRepository->getById($id);
        if (!$item) return false;

        return $this->orderItemRepository->delete($item);
    }
}
