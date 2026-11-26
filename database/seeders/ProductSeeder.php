<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public const DEFAULT_CURRENCY = 'VNĐ';

    private const CAPPUCCINO = [
        'category_id' => 1,
        'name' => 'Cappuchino',
        'description' => 'Cappuccino is a coffee drink that today is typically composed of a single espresso shot and hot milk',
        'price' => 35000,
        'display_image_url' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93',
    ];

    private const CHICKEN_SALAD = [
        'category_id' => 2,
        'name' => 'Chicken satay salad',
        'description' => 'Marinate chicken breasts, then drizzle with a punchy peanut satay sauce',
        'price' => 50000,
        'display_image_url' => 'https://images.unsplash.com/photo-1509042239860-f550ce710b93',
    ];

    private function getProducts()
    {
        // ids 1-6: cafe, ids 7-12: chicken (same data as the lab tutorial)
        return collect(range(1, 12))->map(fn ($id) => ['id' => $id] + ($id <= 6 ? self::CAPPUCCINO : self::CHICKEN_SALAD));
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getProducts() as $product) {
            $product['currency'] = self::DEFAULT_CURRENCY;

            Product::create($product);
        }
    }
}
