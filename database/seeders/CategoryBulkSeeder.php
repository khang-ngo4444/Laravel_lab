<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/**
 * Exercise: 1000 categories to try API pagination (?page=N).
 * Run: php artisan db:seed --class=CategoryBulkSeeder
 */
class CategoryBulkSeeder extends Seeder
{
    public function run(): void
    {
        Category::factory()->count(1000)->create();
    }
}
