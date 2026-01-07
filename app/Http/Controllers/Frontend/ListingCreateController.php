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

    // conditional
    'package_size'  => 'required_if:tipas,preke|in:XS,S,M,L',
    'kiekis'        => 'required_if:tipas,preke|integer|min:1',
    'is_renewable'  => 'nullable|boolean',
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
            ->with('success', 'Skelbimas sėkmingai sukurtas!');
    }

    public function edit(Listing $listing)
    {
        if ($listing->user_id !== auth()->id()) {
            abort(403);
        }

        // Prevent editing non-renewable sold-out items
        if ($listing->is_hidden && $listing->is_renewable == 0) {
            abort(403, 'Šis išparduotas skelbimas negali būti redaguojamas.');
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
    'kiekis'        => 'required_if:tipas,preke|integer|min:1',
    'package_size'  => 'required_if:tipas,preke|in:XS,S,M,L',
    'is_renewable'  => 'nullable|boolean',
    'photos.*'      => 'nullable|image|max:4096',
]);
        $data['is_renewable'] = $request->has('is_renewable') ? 1 : 0;

         $this->listingService->update($listing->id, $data);

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
            ->with('success', 'Skelbimas sėkmingai atnaujintas!');
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
        return back()->with('error', 'Skelbimas privalo turėti bent vieną nuotrauką.');
    }

    Storage::disk('public')->delete($photo->failo_url);
    $photo->delete();

    return back()->with('success', 'Nuotrauka sėkmingai ištrinta.');
}

}
