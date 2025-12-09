<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\FavoriteResource;
use App\Services\FavoriteService;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Requests\UpdateFavoriteRequest;
use App\Http\Resources\BaseCollection;

class FavoriteController extends BaseController
{
    protected FavoriteService $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function index()
    {
        $favorites = $this->favoriteService->getAll();
        return $this->sendResponse(new BaseCollection($favorites, FavoriteResource::class), 'Favorites retrieved.');
    }

    public function show($id)
    {
        $favorite = $this->favoriteService->getById($id);
        if (!$favorite) return $this->sendError('Favorite not found.', 404);

        return $this->sendResponse(new FavoriteResource($favorite), 'Favorite found.');
    }

    public function store(StoreFavoriteRequest $request)
{
    try {
        $favorite = $this->favoriteService->create($request->validated());
        return $this->sendResponse(new FavoriteResource($favorite), 'Favorite created.', 201);

    } catch (\Exception $e) {
        return $this->sendError($e->getMessage(), 400);
    }
}

    public function update(UpdateFavoriteRequest $request, $id)
    {
        $favorite = $this->favoriteService->update($id, $request->validated());
        if (!$favorite) return $this->sendError('Favorite not found.', 404);

        return $this->sendResponse(new FavoriteResource($favorite), 'Favorite updated.');
    }

    public function destroy($id)
    {
        $deleted = $this->favoriteService->delete($id);
        if (!$deleted) return $this->sendError('Favorite not found.', 404);

        return $this->sendResponse(null, 'Favorite deleted.');
    }
}
