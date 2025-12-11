<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ListingPhotoResource;
use App\Services\ListingPhotoService;
use App\Http\Requests\StoreListingPhotoRequest;
use App\Http\Requests\UpdateListingPhotoRequest;
use App\Http\Resources\BaseCollection;

class ListingPhotoController extends BaseController
{
    protected ListingPhotoService $listingPhotoService;

    public function __construct(ListingPhotoService $listingPhotoService)
    {
        $this->listingPhotoService = $listingPhotoService;
    }

    public function index()
    {
        $photos = $this->listingPhotoService->getAll();
        return $this->sendResponse(new BaseCollection($photos, ListingPhotoResource::class), 'Listing photos retrieved.');
    }

    public function show($id)
    {
        $photo = $this->listingPhotoService->getById($id);
        if (!$photo) return $this->sendError('Listing photo not found.', 404);

        return $this->sendResponse(new ListingPhotoResource($photo), 'Listing photo found.');
    }

    public function store(StoreListingPhotoRequest $request)
    {
        $photo = $this->listingPhotoService->create($request->validated());
        return $this->sendResponse(new ListingPhotoResource($photo), 'Listing photo created.', 201);
    }

    public function update(UpdateListingPhotoRequest $request, $id)
    {
        $photo = $this->listingPhotoService->update($id, $request->validated());
        if (!$photo) return $this->sendError('Listing photo not found.', 404);

        return $this->sendResponse(new ListingPhotoResource($photo), 'Listing photo updated.');
    }

    public function destroy($id)
{
    $photo = ListingPhoto::find($id);

    if (!$photo) {
        return $this->sendError('Listing photo not found.', 404);
    }

    $listing = $photo->listing;

    if ($listing->photos()->count() <= 1) {
        return $this->sendError('You must keep at least one photo.', 400);
    }

    Storage::delete('public/' . $photo->failo_url);

    $photo->delete();

    return $this->sendResponse(null, 'Listing photo deleted.');
}

}
