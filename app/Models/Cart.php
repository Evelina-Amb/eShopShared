<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';
    protected $fillable = ['user_id', 'listing_id', 'kiekis'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Listing()
    {
        return $this->belongsTo(Listing::class, 'listing_id');
    }
}

