<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreListingRequest;
use App\Http\Requests\UpdateListingRequest;
use App\Http\Resources\ListingResource;
use App\Services\ListingService;
use App\Models\Listing; 

class ListingController extends BaseController
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    /**
     * List all listings OR listings by IDs (favorites)
     */
    public function index(Request $request)
    {
        if ($request->has('ids')) {
            $ids = array_filter(explode(',', $request->ids));

            $listings = Listing::whereIn('id', $ids)
                ->with(['photos', 'category', 'user'])
                ->get();

            return response()->json([
                'data' => ListingResource::collection($listings),
            ]);
        }

        $listings = Listing::with(['photos', 'category', 'user'])->get();

        return response()->json([
            'data' => ListingResource::collection($listings),
        ]);
    }

    /**
     * Listings belonging to the authenticated user
     */
    public function mine(Request $request)
    {
        $userId = $request->user_id;
        $listings = $this->listingService->getMine($userId);

        return $this->sendResponse(
            ListingResource::collection($listings),
            'Your listings retrieved.'
        );
    }

    /**
     * Show single listing
     */
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

    /**
     * Create listing
     */
    public function store(StoreListingRequest $request)
    {
        $listing = $this->listingService->create($request->validated());

        return $this->sendResponse(
            new ListingResource($listing),
            'Listing created.',
            201
        );
    }

    /**
     * Search listings
     */
    public function search(Request $request)
    {
        $filters = $request->only([
            'q',
            'category_id',
            'tipas',
            'min_price',
            'max_price',
            'sort'
        ]);

        $results = $this->listingService->search($filters);

        return $this->sendResponse(
            ListingResource::collection($results),
            'Search results retrieved.'
        );
    }

    /**
     * Update listing
     */
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

    /**
     * Delete listing
     */
    public function destroy($id)
    {
        $deleted = $this->listingService->delete($id);

        if (!$deleted) {
            return $this->sendError('Listing not found.', 404);
        }

        return $this->sendResponse(null, 'Listing deleted.');
    }
}
