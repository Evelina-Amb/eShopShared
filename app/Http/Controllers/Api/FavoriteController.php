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

   public function store(Request $request)
{
    $data = $request->validate([
        'listing_id' => 'required|exists:listing,id',
    ]);

    $favorite = Favorite::firstOrCreate([
        'user_id' => auth()->id(),
        'listing_id' => $data['listing_id'],
    ]);

    return response()->json($favorite, 201);
}

public function destroy($listingId)
{
    Favorite::where('user_id', auth()->id())
        ->where('listing_id', $listingId)
        ->delete();

    return response()->noContent();
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
