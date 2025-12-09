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
        'kiekis', 'is_renewable',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

   public function Category()
   {
    return $this->belongsTo(Category::class, 'category_id');
   }

    public function ListingPhoto()
    {
        return $this->hasMany(ListingPhoto::class, 'listing_id', 'id');
    }

    public function Review()
    {
        return $this->hasMany(Review::class, 'listing_id', 'id');
    }

    public function averageRating()
    {
        return $this->review()->avg('ivertinimas');
    }
}
