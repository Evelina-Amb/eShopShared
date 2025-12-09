<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    
    protected $table = 'category';

    protected $fillable = ['pavadinimas', 'aprasymas', 'tipo_zenklas'];

    public function Listing()
    {
         return $this->hasMany(Listing::class, 'category_id');
    }
}
