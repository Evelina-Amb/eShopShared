<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Services\ListingService;

class ListingController extends BaseController
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function index(Request $request)
    {
        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $listings = $this->listingService->getByIds($ids);

            return $this->sendResponse(
                ListingResource::collection($listings),
                'Favorites retrieved.'
            );
        }

        $listings = $this->listingService->getAll();

        return $this->sendResponse(
            ListingResource::collection($listings),
            'Listings retrieved.'
        );
    }

    public function mine(Request $request)
    {
        $userId = $request->user_id;
        $listings = $this->listingService->getMine($userId);

        return $this->sendResponse(
            ListingResource::collection($listings),
            'Your listings retrieved.'
        );
    }

    public function show($id)
    {
        $listing = $this->listingService->getById($id);

        if (!$listing) {
            return $this->sendError('Listing not found.', 404);
        }

        return $this->sendResponse(
            new ListingResource($listing),
            'Listing found.'
        );
    }

    public function store(StoreListingRequest $request)
    {
        $listing = $this->listingService->create($request->validated());

        return $this->sendResponse(
            new ListingResource($listing),
            'Listing created.',
            201
        );
    }

    public function search(Request $request)
    {
        $filters = $request->only([
            'q',
            'category_id',
            'tipas',
            'min_price',
            'max_price',
            'sort',
            'city_id',
        ]);

        $results = $this->listingService->search($filters);

        return $this->sendResponse(
            ListingResource::collection($results),
            'Search results retrieved.'
        );
    }

    public function update(UpdateListingRequest $request, $id)
    {
        try {
            $listing = $this->listingService->update($id, $request->validated());

            if (!$listing) {
                return $this->sendError('Listing not found.', 404);
            }

            return $this->sendResponse(
                new ListingResource($listing),
                'Listing updated.'
            );
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 400);
        }
    }


public function destroy($id)
{
    try {
        $listing = $this->listingService->getById($id);

        if (!$listing) {
            return $this->sendError("Listing not found", 404);
        }

        $result = $this->listingService->delete($listing);

        return $this->sendResponse(
            ['action' => $result],
            'Listing processed.'
        );
    } catch (\Throwable $e) {
        return $this->sendError(
            "Server error: " . $e->getMessage(),
            500
        );
    }
}
    
}
