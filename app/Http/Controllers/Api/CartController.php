<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Http\Requests\StoreCartRequest;
use App\Http\Requests\UpdateCartRequest;
use App\Http\Resources\BaseCollection;
use Illuminate\Http\Request;

class CartController extends BaseController
{
    protected CartService $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $carts = $this->cartService->getAll();
        return $this->sendResponse(new BaseCollection($carts, CartResource::class), 'Krepšelio prekės gautos.');
    }

    public function show($id)
    {
        $item = $this->cartService->getById($id);
        if (!$item) {
            return $this->sendError('Krepšelio prekė nerasta.', 404);
        }

        return $this->sendResponse(new CartResource($item), 'Krepšelio prekė rasta..');
    }

    public function store(StoreCartRequest $request)
    {
        try {
            $item = $this->cartService->create($request->validated());
            return $this->sendResponse(new CartResource($item), 'Krepšelio prekė sukurta.', 201);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }

    public function update(UpdateCartRequest $request, $id)
    {
        try {
            $item = $this->cartService->update($id, $request->validated());
            if (!$item) {
                return $this->sendError('Cart item not found.', 404);
            }

            return $this->sendResponse(new CartResource($item), 'Krepšelio prekė nerasta.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }

    public function clearItem(Request $request)
    {
        $userId    = $request->user_id;
        $listingId = $request->listing_id;

        if (!$userId || !$listingId) {
            return $this->sendError('Būtini laukai: user_id ir listing_id', 400);
        }

        $deleted = $this->cartService->clearItem($userId, $listingId);

        return $this->sendResponse($deleted, 'Prekė pašalinta iš krepšelio.');
    }

    public function clearAll(Request $request)
    {
        $userId = $request->user_id;

        if (!$userId) {
            return $this->sendError('Privalomas laukas: user_id', 400);
        }

        $deleted = $this->cartService->clearAll($userId);

        return $this->sendResponse($deleted, 'Krepšelis išvalytas.');
    }

    public function destroy($id)
    {
        $deleted = $this->cartService->delete($id);

        if (!$deleted) {
            return $this->sendError('Krepšelio prekė nerasta.', 404);
        }

        return $this->sendResponse(null, 'Krepšelio prekė ištrinta.');
    }

    public function decrease($id)
{
    $item = Cart::find($id);

    if (!$item) {
        return back()->with('error', 'Krepšelio prekė nerasta.');
    }

    // Prevent going below 1
    if ($item->kiekis <= 1) {
        return back()->with('error', 'Negalite sumažinti kiekio žemiau 1. Pašalinkite prekę iš krepšelio.');
    }

    $item->kiekis -= 1;
    $item->save();

    return back()->with('success', 'Kiekis atnaujintas.');
}

}
