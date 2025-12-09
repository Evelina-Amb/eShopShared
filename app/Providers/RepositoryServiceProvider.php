<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Repositories\Contracts\ListingRepositoryInterface;
use App\Repositories\ListingRepository;
use App\Repositories\Contracts\CountryRepositoryInterface;
use App\Repositories\CountryRepository;
use App\Repositories\Contracts\CityRepositoryInterface;
use App\Repositories\CityRepository;
use App\Repositories\Contracts\AddressRepositoryInterface;
use App\Repositories\AddressRepository;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Contracts\ListingPhotoRepositoryInterface;
use App\Repositories\ListingPhotoRepository;
use App\Repositories\Contracts\ReviewRepositoryInterface;
use App\Repositories\ReviewRepository;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\OrderRepository;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\CartRepository;
use App\Repositories\Contracts\FavoriteRepositoryInterface;
use App\Repositories\FavoriteRepository;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\OrderItemRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            ListingRepositoryInterface::class,
            ListingRepository::class
        );

        $this->app->bind(
        CountryRepositoryInterface::class,
        CountryRepository::class
    );

         $this->app->bind(
        CityRepositoryInterface::class, 
        CityRepository::class
    );

        $this->app->bind(
        AddressRepositoryInterface::class, 
        AddressRepository::class
    );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );

        $this->app->bind(
            UserRepositoryInterface::class, 
            UserRepository::class
        );

        $this->app->bind(
            ListingPhotoRepositoryInterface::class, 
            ListingPhotoRepository::class
        );

        $this->app->bind(
            ReviewRepositoryInterface::class, 
            ReviewRepository::class
        );

        $this->app->bind(
            OrderRepositoryInterface::class, 
            OrderRepository::class
        );
        
        $this->app->bind(
            CartRepositoryInterface::class, 
            CartRepository::class
        );

        $this->app->bind(
            FavoriteRepositoryInterface::class, 
            FavoriteRepository::class
        );

        $this->app->bind(
            OrderItemRepositoryInterface::class, 
            OrderItemRepository::class
        );


    }

    public function boot()
    {
        //
    }
}
