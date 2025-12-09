<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::insert([
            ['pavadinimas' => 'Lithuania'],
            ['pavadinimas' => 'Latvia'],
            ['pavadinimas' => 'Estonia'],
        ]);
    }
}
