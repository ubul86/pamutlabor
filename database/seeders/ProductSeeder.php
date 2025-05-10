<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Tag;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $tags = Tag::all();

        if ($tags->isEmpty()) {
            $this->command->warn('There are no tags');
        }

        Product::factory(20)->create()->each(function ($product) use ($tags) {
            if ($tags->isNotEmpty()) {
                $product->tags()->attach(
                    $tags->random(rand(1, 4))->pluck('id')->toArray()
                );
            }
        });
    }
}
