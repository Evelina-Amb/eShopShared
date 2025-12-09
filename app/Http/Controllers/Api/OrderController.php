<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\BaseCollection;
use App\Services\OrderService;

class OrderController extends BaseController
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index()
    {
        $orders = Order::with(['user', 'orderItem'])->get();
        return $this->sendResponse(new BaseCollection($orders,  OrderResource::class),  'Pirkimai gauti.');
    }

    public function show($id)
    {
        $item = Order::with(['user', 'orderItem'])->find($id);
        if (!$item) return $this->sendError('Pirkimas nerastas', 404);

        return $this->sendResponse(new OrderResource($item), 'Pirkimas rastas.');
    }

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->create($request->validated());
            return $this->sendResponse(new OrderResource($order), 'Pirkimas sukurtas.', 201);

            } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }

    public function update()
    {
        return $this->sendError('Orders cannot be updated.', 403);
    }

    public function destroy()
    {
        return $this->sendError('Orders cannot be deleted.', 403);
    }

}
