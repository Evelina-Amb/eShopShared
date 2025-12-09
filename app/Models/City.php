<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table = 'city';
    protected $fillable = ['pavadinimas', 'country_id'];

    public function Country()
    {
        return $this->belongsTo(Country::class);
    }

    public function Address()
    {
        return $this->hasMany(Address::class);
    }
}
