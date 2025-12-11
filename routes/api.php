<?php

use App\Http\Controllers\Api\{
    CountryController, CityController, AddressController,
    CategoryController, ListingPhotoController,
    ReviewController, CartController,
    FavoriteController, OrderController, OrderItemController,
    UserController, ListingController
};
use App\Models\City;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {

    Route::get('/listings/mine', [ListingController::class, 'mine']);
    Route::get('/listings/search', [ListingController::class, 'search']);

    Route::delete('/cart/item', [CartController::class, 'clearItem']);
    Route::delete('/cart/clear', [CartController::class, 'clearAll']);

    Route::post('/users/{id}/ban', [UserController::class, 'ban'])->middleware('admin');
    Route::post('/users/{id}/unban', [UserController::class, 'unban'])->middleware('admin');

    Route::get('/cities/by-country/{countryId}', function ($countryId) {
        return City::where('country_id', $countryId)
            ->get(['id', 'pavadinimas']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/favorites/ids', function () {
            return auth()->user()
                ->favoriteListings()
                ->pluck('listing_id');
        });

        Route::get('/favorites/my', function () {
            return auth()->user()
                ->favoriteListings()
                ->with(['photos', 'category', 'user'])
                ->get();
        });

        Route::post('/favorite', [FavoriteController::class, 'store']);
        Route::delete('/favorite/{listingId}', [FavoriteController::class, 'destroyByListing']);
    });

    Route::apiResources([
        'country'     => CountryController::class,
        'city'        => CityController::class,
        'address'     => AddressController::class,
        'category'    => CategoryController::class,
        'listingPhoto'=> ListingPhotoController::class,
        'review'      => ReviewController::class,
        'cart'        => CartController::class,
        'order'       => OrderController::class,
        'orderItem'   => OrderItemController::class,
        'users'       => UserController::class,
        'listing'     => ListingController::class,
    ]);
});

