<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FavoriteService;
use App\Http\Requests\StoreFavoriteRequest;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    protected FavoriteService $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    /**
     * POST /api/favorite
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
     * DELETE /api/favorite/{listingId}
     */
    public function destroyByListing(int $listingId)
    {
        Favorite::where('user_id', auth()->id())
            ->where('listing_id', $listingId)
            ->delete();

        return response()->json(['ok' => true]);
    }
}
