<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\FavoriteResource;
use App\Http\Resources\BaseCollection;
use App\Services\FavoriteService;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Requests\UpdateFavoriteRequest;
use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends BaseController
{
    protected FavoriteService $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    /**
     * GET /api/favorite
     * (admin / future use)
     */
    public function index()
    {
        $favorites = $this->favoriteService->getAll();

        return $this->sendResponse(
            new BaseCollection($favorites, FavoriteResource::class),
            'Favorites retrieved.'
        );
    }

    /**
     * GET /api/favorite/{id}
     * (admin / future use)
     */
    public function show($id)
    {
        $favorite = $this->favoriteService->getById($id);

        if (!$favorite) {
            return $this->sendError('Favorite not found.', 404);
        }

        return $this->sendResponse(
            new FavoriteResource($favorite),
            'Favorite found.'
        );
    }

    /**
     * POST /api/favorite
     * Add favorite (frontend)
     */
    public function store(StoreFavoriteRequest $request)
    {
        try {
            $favorite = $this->favoriteService->create([
                'user_id' => auth()->id(),
                'listing_id' => $request->listing_id,
            ]);

            return response()->json($favorite, 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * PUT /api/favorite/{id}
     * (admin / future use)
     */
    public function update(UpdateFavoriteRequest $request, $id)
    {
        $favorite = $this->favoriteService->update($id, $request->validated());

        if (!$favorite) {
            return $this->sendError('Favorite not found.', 404);
        }

        return $this->sendResponse(
            new FavoriteResource($favorite),
            'Favorite updated.'
        );
    }

    /**
     * DELETE /api/favorite/{listingId}
     */
    public function destroy($listingId)
    {
        Favorite::where('user_id', auth()->id())
            ->where('listing_id', $listingId)
            ->delete();

        return response()->json(['ok' => true]);
    }
}
