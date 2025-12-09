<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingRegistration extends Model
{
    protected $fillable = [
        'vardas',
        'pavarde',
        'el_pastas',
        'slaptazodis',
        'token',
        'expires_at',
    ];

    public $timestamps = true;
}
