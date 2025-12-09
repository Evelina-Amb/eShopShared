<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Frontend\HomeSearchController;
use App\Http\Controllers\Frontend\MyListingsController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\ListingCreateController;
use App\Http\Controllers\Frontend\FavoriteController;
use App\Http\Controllers\Frontend\ReviewController;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeSearchController::class, 'search'])->name('search.listings');

Route::get('/favorites', fn() => view('frontend.favorites'))->name('favorites.page');

Route::middleware('auth')->group(function () {
    //DELETE PHOTO
    Route::delete('/listing/{listing}/photo/{photo}', 
        [ListingCreateController::class, 'deletePhoto'])
        ->name('listing.photo.delete');
    
    // CART
    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart/add/{listing}', [CartController::class, 'add'])
        ->name('cart.add');

    Route::post('/cart/increase/{cart}', [CartController::class, 'increase'])
        ->name('cart.increase');

    Route::post('/cart/decrease/{cart}', [CartController::class, 'decrease'])
        ->name('cart.decrease');

    Route::delete('/cart/remove/{cart}', [CartController::class, 'remove'])
        ->name('cart.remove');

    Route::post('/cart/checkout', [CartController::class, 'checkout'])
        ->name('cart.checkout');

    Route::delete('/cart/clear', [CartController::class, 'clearAll'])->name('cart.clear');

    Route::post('/listing/{listing}/review', [ReviewController::class, 'store'])->name('review.store');

    Route::middleware('seller')->group(function () {
    // LISTING CREATE
    Route::get('/listing/create', [ListingCreateController::class, 'create'])
        ->name('listing.create');

    Route::post('/listing/create', [ListingCreateController::class, 'store'])
        ->name('listing.store');

    // LISTING EDIT/UPDATE
    Route::get('/listing/{listing}/edit', [ListingCreateController::class, 'edit'])
        ->name('listing.edit');

    Route::put('/listing/{listing}', [ListingCreateController::class, 'update'])
        ->name('listing.update');

    // MY LISTINGS
    Route::get('/my-listings', [MyListingsController::class, 'index'])
        ->name('my.listings');

    });

});

//VIEW SINGLE LISTING
Route::get('/listing/{listing}', [HomeController::class, 'show'])
    ->name('listing.single');


//USER PROFILE
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::put('/password', [ProfileController::class, 'updatePassword'])
        ->name('password.update');
});

//EMAIL VERIFICATION
Route::get('/verify-email', fn() => view('auth.pending-verification'))
    ->name('verify.notice');

Route::post('/verify-email/resend', [RegisteredUserController::class, 'resend'])
    ->name('verify.resend');

Route::get('/verify/{token}', [RegisteredUserController::class, 'verify'])
    ->name('verify.complete');

Route::get('/email/verify-new/{token}', [ProfileController::class, 'verifyNewEmail'])
     ->name('email.verify.new');


require __DIR__.'/auth.php';
