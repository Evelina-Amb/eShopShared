<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    protected $table = 'review';

    protected $fillable = ['ivertinimas', 'komentaras', 'listing_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function Listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
