<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ListingService;

class HomeSearchController extends Controller
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function search()
    {
        $filters = request()->only(['q', 'category_id', 'tipas', 'min_price', 'max_price', 'city_id']);

        $listings = $this->listingService->search($filters);

        return view('frontend.search-results', [
            'listings' => $listings,
            'filters'  => $filters
        ]);
    }
}
