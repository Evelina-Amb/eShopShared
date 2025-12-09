<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\OrderItemResource;
use App\Services\OrderItemService;
use App\Http\Requests\StoreOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;
use App\Http\Resources\BaseCollection;

class OrderItemController extends BaseController
{
    protected OrderItemService $orderItemService;

    public function __construct(OrderItemService $orderItemService)
    {
        $this->orderItemService = $orderItemService;
    }

    public function index()
    {
        $OrderItems = $this->orderItemService->getAll();
        return $this->sendResponse(new BaseCollection($OrderItems, OrderItemResource::class), 'Order items retrieved.');
    }

    public function show($id)
    {
        $item = $this->orderItemService->getById($id);
        if (!$item) return $this->sendError('Order item not found.', 404);

        return $this->sendResponse(new OrderItemResource($item), 'Order item found.');
    }

    public function store()
    {
        return $this->sendError('Direct creation of order items is not allowed.', 403);
    }

    public function update()
    {
        return $this->sendError('Order items cannot be updated manually.', 403);
    }

    public function destroy()
    {
        return $this->sendError('Order items cannot be deleted manually.', 403);
    }

}
