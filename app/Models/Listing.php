<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $table = 'listing';

    protected $fillable = [
        'pavadinimas', 'aprasymas', 'kaina', 'tipas',
        'user_id', 'category_id', 'statusas', 'is_hidden', 
        'kiekis', 'is_renewable', 'package_size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   public function Category()
   {
    return $this->belongsTo(Category::class, 'category_id');
   }

    public function photos()
{
    return $this->hasMany(ListingPhoto::class, 'listing_id');
}

      public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function Review()
    {
        return $this->hasMany(Review::class, 'listing_id', 'id');
    }

    public function averageRating()
    {
        return $this->review()->avg('ivertinimas');
    }

    public function orderItems()
{
    return $this->hasMany(\App\Models\OrderItem::class);
}

}
