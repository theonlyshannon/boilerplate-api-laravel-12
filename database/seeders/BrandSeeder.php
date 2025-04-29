<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            ['name' => 'Nike'],
            ['name' => 'Adidas'],
            ['name' => 'Puma'],
            ['name' => 'Reebok'],
            ['name' => 'New Balance'],
            ['name' => 'Under Armour'],
            ['name' => 'Converse'],
            ['name' => 'Vans'],
            ['name' => 'Fila'],
            ['name' => 'Asics']
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
