<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route; 
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Frontend\HomeSearchController;
use App\Http\Controllers\Frontend\MyListingsController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\ListingCreateController;
use App\Http\Controllers\Frontend\ReviewController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\StripeConnectController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Models\OrderShipment;
use App\Http\Controllers\Frontend\ListingController;
use App\Http\Controllers\Frontend\SellerOrderController;
use App\Http\Controllers\Frontend\BuyerOrderController;
use App\Http\Controllers\Dev\AdminBootstrapController;

Route::get('/__bootstrap/admin/{user}', [AdminBootstrapController::class, 'promote'])
    ->middleware('auth');


Route::middleware('auth')->group(function () {
    Route::delete('/listing/{listing}', [ListingController::class, 'destroy'])
        ->name('listing.destroy');
});

Route::get('/my/purchases', [BuyerOrderController::class, 'index'])
    ->middleware('auth')
    ->name('buyer.orders');

Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::post('/checkout/shipment', [\App\Http\Controllers\Frontend\CheckoutController::class, 'shipment'])
    ->middleware('auth')
    ->name('checkout.shipment');

Route::post('/checkout/shipping', [\App\Http\Controllers\Frontend\CheckoutController::class, 'shipping'])
    ->middleware('auth')
    ->name('checkout.shipping');

Route::post(
    '/seller/shipments/{shipment}/ship',
    [SellerShipmentController::class, 'ship']
)->middleware('seller');

Route::post('/checkout/shipping/preview',
    [CheckoutController::class, 'previewShipping']
)->middleware('auth');

Route::post('/checkout/intent', [CheckoutController::class, 'intent'])
    ->middleware('auth')
    ->name('checkout.intent');

Route::get('/seller/stripe/dashboard', [
    \App\Http\Controllers\Frontend\StripeConnectController::class,
    'dashboard'
])->middleware('auth')->name('stripe.dashboard');

Route::get('/media/{filename}', function ($filename) {
    $filename = basename($filename);

    $path = "listing_photos/{$filename}";

    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    return response()->file(
        Storage::disk('public')->path($path),
        ['Cache-Control' => 'public, max-age=86400']
    );
})->name('media.show');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeSearchController::class, 'search'])->name('search.listings');

Route::middleware('auth')->get('/favorites', fn () => view('frontend.favorites'))
    ->name('favorites.page');

Route::middleware('auth')->group(function () {

    Route::delete('/listing/{listing}/photo/{photo}', 
        [ListingCreateController::class, 'deletePhoto'])
        ->name('listing.photo.delete');

      Route::get('/seller/stripe/connect', [StripeConnectController::class, 'connect'])
        ->name('stripe.connect');

    Route::get('/seller/stripe/refresh', [StripeConnectController::class, 'refresh'])
        ->name('stripe.refresh');

    Route::get('/seller/stripe/return', [StripeConnectController::class, 'return'])
        ->name('stripe.return');

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

    Route::delete('/cart/clear', [CartController::class, 'clearAll'])
        ->name('cart.clear');

    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::get('/checkout/success', [CheckoutController::class, 'success'])
        ->name('checkout.success');

    Route::post('/listing/{listing}/review', [ReviewController::class, 'store'])
        ->name('review.store');
   
    Route::get('/my/purchases', [BuyerOrderController::class, 'index'])
  ->middleware('auth')
  ->name('buyer.orders');

    Route::middleware('seller')->group(function () {

        Route::get('/listing/create', [ListingCreateController::class, 'create'])
            ->name('listing.create');

        Route::post('/listing/create', [ListingCreateController::class, 'store'])
            ->name('listing.store');

        Route::get('/listing/{listing}/edit', [ListingCreateController::class, 'edit'])
            ->name('listing.edit');

        Route::put('/listing/{listing}', [ListingCreateController::class, 'update'])
            ->name('listing.update');

        Route::get('/my-listings', [MyListingsController::class, 'index'])
            ->name('my.listings');
   
    Route::get('/seller/orders', [SellerOrderController::class, 'index'])
        ->name('seller.orders');

    Route::post('/seller/shipments/{shipment}', [SellerOrderController::class, 'ship'])
        ->name('seller.shipments.update');

    });
});

Route::get('/listing/{listing}', [HomeController::class, 'show'])
    ->name('listing.single');

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

Route::get('/verify-email', fn () => view('auth.pending-verification'))
    ->name('verify.notice');

Route::post('/verify-email/resend', [RegisteredUserController::class, 'resend'])
    ->name('verify.resend');

Route::get('/verify/{token}', [RegisteredUserController::class, 'verify'])
    ->name('verify.complete');

Route::get('/email/verify-new/{token}', [ProfileController::class, 'verifyNewEmail'])
    ->name('email.verify.new');

require __DIR__.'/auth.php';
