<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // siapkan file gambar dummy 1x1 px (PNG) di storage:public/products/
        $png1x1 = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8Xw8AAoMBgT4WqXQAAAAASUVORK5CYII=');
        Storage::disk('public')->put('products/dummy.png', $png1x1);

        $map = [
            'Coffee' => [
                ['Espresso', 15000],
                ['Americano', 18000],
                ['Cappuccino', 25000],
            ],
            'Tea' => [
                ['Jasmine Tea', 12000],
                ['Lemon Tea', 15000],
            ],
            'Milk & Frappe' => [
                ['Chocolate Frappe', 27000],
                ['Matcha Latte', 28000],
            ],
            'Snacks' => [
                ['French Fries', 20000],
                ['Chicken Wings', 32000],
            ],
            'Desserts' => [
                ['Cheesecake Slice', 25000],
                ['Brownies', 18000],
            ],
            'Main Course' => [
                ['Chicken Rice Bowl', 35000],
                ['Beef Teriyaki Bowl', 42000],
            ],
        ];

        foreach ($map as $catName => $products) {
            $cat = Category::where('name', $catName)->first();
            if (!$cat) continue;

            foreach ($products as [$name, $price]) {
                $slug = Str::slug($name);
                $prod = Product::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'category_id'     => $cat->id,
                        'name'            => $name,
                        'sku'             => strtoupper(Str::random(6)),
                        'description'     => null,
                        'price'           => $price,
                        'discount_price'  => null,
                        'stock'           => 50,
                        'is_active'       => true,
                        'options_schema'  => null,
                        'main_image_path' => 'products/dummy.png',
                        'main_image_disk' => 'public',
                    ]
                );

                // satu foto di galeri
                ProductImage::updateOrCreate(
                    ['product_id' => $prod->id, 'path' => 'products/dummy.png'],
                    ['disk' => 'public', 'is_primary' => true, 'sort_order' => 0]
                );
            }
        }
    }
}
