<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        City::insert([
        ['country_id' => 1, 'pavadinimas' => 'Vilnius'],
        ['country_id' => 1, 'pavadinimas' => 'Kaunas'],
        ]);
    }
}
