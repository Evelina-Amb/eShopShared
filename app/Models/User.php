<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'vardas',
        'pavarde',
        'el_pastas',
        'slaptazodis',
        'telefonas',
        'address_id',
        'role',
        'is_banned',
        'ban_reason',
        'banned_at',
        'business_email',
        'stripe_account_id',
        'stripe_onboarded',
    ];

    protected $hidden = [
        'slaptazodis',
        'remember_token',
    ];

    protected $casts = [
        'slaptazodis' => 'hashed',
        'banned_at' => 'datetime',
        'stripe_onboarded' => 'boolean',
    ];

    public function getAuthPassword()
    {
        return $this->slaptazodis;
    }

    public function Address()
    {
        return $this->belongsTo(Address::class);
    }

    public function Listing()
    {
        return $this->hasMany(Listing::class);
    }

    public function listings()
    {
        return $this->Listing();
    }

    public function Review()
    {
        return $this->hasMany(Review::class);
    }

    public function Cart()
    {
        return $this->hasMany(Cart::class);
    }

    public function Favorite()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteListings()
    {
        return $this->belongsToMany(
            Listing::class,
            'favorite',
            'user_id',
            'listing_id'
        )->withTimestamps();
    }

    public function Order()
    {
        return $this->hasMany(Order::class);
    }

    public function getEmailForPasswordReset()
    {
        return $this->el_pastas;
    }

    public function routeNotificationForMail()
    {
        return $this->el_pastas;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(
            new \App\Notifications\ResetPasswordNotification($token)
        );
    }
}
