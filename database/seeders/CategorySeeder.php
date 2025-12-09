<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::upsert([
            ['pavadinimas' => 'Electronics'],
            ['pavadinimas' => 'Furniture'],
            ['pavadinimas' => 'Clothing'],
            ['pavadinimas' => 'Books'],
            ['pavadinimas' => 'Home & Kitchen'],
            ['pavadinimas' => 'Sports'],
        ], ['pavadinimas']);
    }
}
