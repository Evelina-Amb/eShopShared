<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ListingService;
use App\Models\Listing;
use App\Models\OrderItem;

class HomeController extends Controller
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function index()
    {
        $filters = request()->only(['tipas', 'sort']);

        if (empty($filters['tipas'])) {
            $listings = $this->listingService->search([
                'sort' => $filters['sort'] ?? null,
            ]);
        } else {
            $listings = $this->listingService->search($filters);
        }

        return view('frontend.home', [
            'listings' => $listings,
            'filters'  => $filters,
        ]);
    }
    
       public function show(Listing $listing)
{
    $listing->load(['photos', 'user', 'category', 'review.user']);

    $similar = Listing::where('user_id', $listing->user_id)
        ->where('id', '!=', $listing->id)
        ->where('is_hidden', 0)
        ->where('statusas', '!=', 'parduotas')
        ->with('photos')
        ->take(4)
        ->get();

    $hasPurchased = false;

    if (auth()->check()) {
        $hasPurchased = OrderItem::where('listing_id', $listing->id)
            ->whereHas('order', function ($q) {
                $q->where('user_id', auth()->id())
                  ->where('statusas', 'completed');
            })
            ->exists();
    }

    $reviewsAllowed = $listing->is_renewable || $listing->kiekis > 5;

    return view('frontend.listing-single', [
        'listing'        => $listing,
        'similar'        => $similar,
        'hasPurchased'   => $hasPurchased,
        'reviewsAllowed' => $reviewsAllowed,
    ]);
}

}
