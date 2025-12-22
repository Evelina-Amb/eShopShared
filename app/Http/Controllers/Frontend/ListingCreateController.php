<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ListingService;
use App\Models\Listing;
use App\Models\ListingPhoto;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ListingCreateController extends Controller
{
    protected ListingService $listingService;

    public function __construct(ListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    public function create()
    {
        $categories = Category::all();
        return view('frontend.listing-create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pavadinimas'   => 'required|string|max:255',
            'aprasymas'     => 'required|string',
            'kaina'         => 'required|numeric|min:0',
            'tipas'         => 'required|in:preke,paslauga',
            'category_id'   => 'required|exists:category,id',
            'photos.*'      => 'nullable|image|max:4096',
            'kiekis'        => 'required|integer|min:1',
            'is_renewable'  => 'nullable|boolean',
            'package_size'  => 'required|in:S,M,L',
        ]);

        $data['user_id']      = auth()->id();
        $data['statusas']     = 'aktyvus';
        $data['is_renewable'] = $request->has('is_renewable') ? 1 : 0;

        // Create listing
        $listing = $this->listingService->create($data);

        // Upload photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('listing_photos', 'public');

                ListingPhoto::create([
                    'listing_id' => $listing->id,
                    'failo_url' => $path,
                ]);
            }
        }

        return redirect()
            ->route('listing.single', $listing->id)
            ->with('success', 'Listing created successfully!');
    }

    public function edit(Listing $listing)
    {
        if ($listing->user_id !== auth()->id()) {
            abort(403);
        }

        // Prevent editing non-renewable sold-out items
        if ($listing->is_hidden && $listing->is_renewable == 0) {
            abort(403, 'This sold-out item cannot be edited.');
        }

        $categories = Category::all();

        return view('frontend.listing-edit', compact('listing', 'categories'));
    }

    public function update(Request $request, Listing $listing)
    {
        if ($listing->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'pavadinimas'   => 'required|string|max:255',
            'aprasymas'     => 'required|string',
            'kaina'         => 'required|numeric|min:0',
            'tipas'         => 'required|in:preke,paslauga',
            'category_id'   => 'required|exists:category,id',
            'kiekis'        => 'required|integer|min:1',
            'is_renewable'  => 'nullable|boolean',
            'photos.*'      => 'nullable|image|max:4096',
            'package_size'  => 'required|in:S,M,L',
        ]);

        $data['is_renewable'] = $request->has('is_renewable') ? 1 : 0;

        // Update listing
        $listing->update($data);

        // Add new photos
        if ($request->hasFile('photos')) {
            foreach ($request->photos as $photo) {
                $path = $photo->store('listing_photos', 'public');

                ListingPhoto::create([
                    'listing_id' => $listing->id,
                   'failo_url'  => $path,
                ]);
            }
        }

        return redirect()
            ->route('listing.single', $listing->id)
            ->with('success', 'Listing updated successfully!');
    }

   public function deletePhoto(Listing $listing, ListingPhoto $photo)
{
    if ($listing->user_id !== auth()->id()) {
        abort(403);
    }

    if ($photo->listing_id !== $listing->id) {
        abort(404);
    }

    if ($listing->photos()->count() <= 1) {
        return back()->with('error', 'A listing must have at least one photo.');
    }

    Storage::disk('public')->delete($photo->failo_url);
    $photo->delete();

    return back()->with('success', 'Photo deleted successfully.');
}

}
