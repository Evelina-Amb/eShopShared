<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::upsert([
            [
                'pavadinimas' => 'Keramika',
                'aprasymas' => 'Rankų darbo molio gaminiai',
                'tipo_zenklas' => 'preke',
            ],
            [
                'pavadinimas' => 'Mediena',
                'aprasymas' => 'Rankų darbo mediniai gaminiai',
                'tipo_zenklas' => 'preke',
            ],
            [
                'pavadinimas' => 'Papuošalai',
                'aprasymas' => 'Rankų darbo papuošalai',
                'tipo_zenklas' => 'preke',
            ],
            [
                'pavadinimas' => 'Tekstilė',
                'aprasymas' => 'Rankų darbo tekstilės gaminiai',
                'tipo_zenklas' => 'preke',
            ],
            [
                'pavadinimas' => 'Skaitmeniniai',
                'aprasymas' => 'Skaitmeniniai produktai',
                'tipo_zenklas' => 'paslauga',
            ],
        ], ['pavadinimas'], ['aprasymas', 'tipo_zenklas']);
    }
}
