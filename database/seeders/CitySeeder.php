<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        City::insert([
        ['country_id' => 1, 'pavadinimas' => 'Vilnius'],
        ['country_id' => 1, 'pavadinimas' => 'Kaunas'],
        ]);
    }
}
