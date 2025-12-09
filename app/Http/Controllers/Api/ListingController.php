<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ListingResource;
use App\Models\Listing;
use App\Services\ListingService;
use Illuminate\Http\Request;

class ListingController extends BaseController
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    /**
     * List all listings OR favorites by IDs
     * /api/listing
     * /api/listing?ids=1,2,3
     */
    public function index(Request $request)
    {
        if ($request->filled('ids')) {
            $ids = array_map('intval', explode(',', $request->ids));

            $listings = Listing::whereIn('id', $ids)
                ->with(['photos', 'category', 'user'])
                ->get();

            return $this->sendResponse(
                ListingResource::collection($listings),
                'Favorite listings retrieved.'
            );
        }

        $listings = Listing::with(['photos', 'category', 'user'])->get();

        return $this->sendResponse(
            ListingResource::collection($listings),
            'Listings retrieved.'
        );
    }

    /**
     * Show single listing
     */
    public function show($id)
    {
        $listing = Listing::with(['photos', 'category', 'user'])->find($id);

        if (!$listing) {
            return $this->sendError('Listing not found.', 404);
        }

        return $this->sendResponse(
            new ListingResource($listing),
            'Listing retrieved.'
        );
    }

    /**
     * Listings owned by user
     */
    public function mine(Request $request)
    {
        $userId = $request->user()->id;

        $listings = $this->listingService->getMine($userId);

        return $this->sendResponse(
            ListingResource::collection($listings),
            'Your listings retrieved.'
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
            'sort',
        ]);

        $results = $this->listingService->search($filters);

        return $this->sendResponse(
            ListingResource::collection($results),
            'Search results retrieved.'
        );
    }
}
