<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['listing_id', 'failo_url'];

    public function listing() 
    {
        return $this->belongsTo(Listing::class);
    }
}
