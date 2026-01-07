<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\City;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('city')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        City::insert([
            ['country_id' => 1, 'pavadinimas' => 'Vilnius'],
            ['country_id' => 1, 'pavadinimas' => 'Kaunas'],
            ['country_id' => 1, 'pavadinimas' => 'Klaipėda'],
            ['country_id' => 1, 'pavadinimas' => 'Šiauliai'],
            ['country_id' => 1, 'pavadinimas' => 'Panevėžys'],
            ['country_id' => 1, 'pavadinimas' => 'Alytus'],
            ['country_id' => 1, 'pavadinimas' => 'Marijampolė'],
            ['country_id' => 1, 'pavadinimas' => 'Mažeikiai'],
            ['country_id' => 1, 'pavadinimas' => 'Jonava'],
            ['country_id' => 1, 'pavadinimas' => 'Utena'],
            ['country_id' => 1, 'pavadinimas' => 'Kėdainiai'],
            ['country_id' => 1, 'pavadinimas' => 'Telšiai'],
            ['country_id' => 1, 'pavadinimas' => 'Visaginas'],
            ['country_id' => 1, 'pavadinimas' => 'Tauragė'],
            ['country_id' => 1, 'pavadinimas' => 'Ukmergė'],
            ['country_id' => 1, 'pavadinimas' => 'Plungė'],
            ['country_id' => 1, 'pavadinimas' => 'Kretinga'],
            ['country_id' => 1, 'pavadinimas' => 'Šilutė'],
            ['country_id' => 1, 'pavadinimas' => 'Radviliškis'],
            ['country_id' => 1, 'pavadinimas' => 'Druskininkai'],
            ['country_id' => 1, 'pavadinimas' => 'Palanga'],
            ['country_id' => 1, 'pavadinimas' => 'Biržai'],
            ['country_id' => 1, 'pavadinimas' => 'Rokiškis'],
            ['country_id' => 1, 'pavadinimas' => 'Elektrėnai'],
            ['country_id' => 1, 'pavadinimas' => 'Jurbarkas'],
            ['country_id' => 1, 'pavadinimas' => 'Vilkaviškis'],
            ['country_id' => 1, 'pavadinimas' => 'Šilalė'],
            ['country_id' => 1, 'pavadinimas' => 'Anyksčiai'],
            ['country_id' => 1, 'pavadinimas' => 'Prienai'],
            ['country_id' => 1, 'pavadinimas' => 'Kelmė'],
            ['country_id' => 1, 'pavadinimas' => 'Varėna'],
            ['country_id' => 1, 'pavadinimas' => 'Kaišiadorys'],
            ['country_id' => 1, 'pavadinimas' => 'Pasvalys'],
            ['country_id' => 1, 'pavadinimas' => 'Zarasai'],
            ['country_id' => 1, 'pavadinimas' => 'Molėtai'],
            ['country_id' => 1, 'pavadinimas' => 'Širvintos'],
            ['country_id' => 1, 'pavadinimas' => 'Švenčionys'],
            ['country_id' => 1, 'pavadinimas' => 'Kazlų Rūda'],
            ['country_id' => 1, 'pavadinimas' => 'Skuodas'],
            ['country_id' => 1, 'pavadinimas' => 'Ignalina'],
            ['country_id' => 1, 'pavadinimas' => 'Akmenė'],
            ['country_id' => 1, 'pavadinimas' => 'Šakiai'],
            ['country_id' => 1, 'pavadinimas' => 'Pakruojis'],
            ['country_id' => 1, 'pavadinimas' => 'Kalvarija'],
            ['country_id' => 1, 'pavadinimas' => 'Lazdijai'],
            ['country_id' => 1, 'pavadinimas' => 'Neringa'],
            ['country_id' => 1, 'pavadinimas' => 'Pagėgiai'],
            ['country_id' => 1, 'pavadinimas' => 'Rietavas'],
        ]);
    }
}
