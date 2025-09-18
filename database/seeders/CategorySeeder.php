<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            ['name' => 'Coffee'],
            ['name' => 'Tea'],
            ['name' => 'Milk & Frappe'],
            ['name' => 'Snacks'],
            ['name' => 'Desserts'],
            ['name' => 'Main Course'],
        ];

        foreach ($cats as $c) {
            $name = $c['name'];
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => null, 'is_active' => true]
            );
        }
    }
}
