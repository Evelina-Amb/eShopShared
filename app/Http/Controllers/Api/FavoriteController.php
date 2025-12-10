<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\FavoriteResource;
use App\Services\FavoriteService;
use App\Http\Requests\StoreFavoriteRequest;
use App\Http\Requests\UpdateFavoriteRequest;
use App\Http\Resources\BaseCollection;
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
     * Used by admin / future API usage
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
     * Used if you ever want explicit "add favorite"
     */
    public function store(StoreFavoriteRequest $request)
    {
        try {
            $favorite = $this->favoriteService->create(
                array_merge(
                    $request->validated(),
                    ['user_id' => auth()->id()]
                )
            );

            return $this->sendResponse(
                new FavoriteResource($favorite),
                'Favorite created.',
                201
            );

        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }

    /**
     * PUT /api/favorite/{id}
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
     * DELETE /api/favorite/{id}
     * Keeps old behavior intact
     */
    public function destroy($id)
    {
        $deleted = $this->favoriteService->delete($id);

        if (!$deleted) {
            return $this->sendError('Favorite not found.', 404);
        }

        return $this->sendResponse(null, 'Favorite deleted.');
    }

    /**
     * ✅ POST /api/favorites/toggle
     * ✅ THIS is the working logic from your other project
     * ✅ Used by frontend heart button
     */
    public function toggle(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'listing_id' => 'required|exists:listing,id',
        ]);

        $favorite = Favorite::where('user_id', $user->id)
            ->where('listing_id', $request->listing_id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return response()->json([
                'favorited' => false,
            ]);
        }

        Favorite::create([
            'user_id' => $user->id,
            'listing_id' => $request->listing_id,
        ]);

        return response()->json([
            'favorited' => true,
        ]);
    }
}
