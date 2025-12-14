<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $categories = [
            ['name' => 'Kain'],
            ['name' => 'Benang'],
            ['name' => 'Aksesoris'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate($category);
        }

        // Create suppliers
        $suppliers = [
            ['name' => 'Supplier A', 'contact' => '081234567890'],
            ['name' => 'Supplier B', 'contact' => '081234567891'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate($supplier);
        }
    }
}

