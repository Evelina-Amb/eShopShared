<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\Cart;

class SyncCartCount
{
    public function handle(Login $event): void
    {
        $count = Cart::where('user_id', $event->user->id)
            ->sum('kiekis');

        session()->put('cart_count', $count);
    }
}
