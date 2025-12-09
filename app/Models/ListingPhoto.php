<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingPhoto extends Model
{
    use HasFactory;
    protected $table = 'listing_photo';

    protected $fillable = ['listing_id', 'failo_url'];

    public function Listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
