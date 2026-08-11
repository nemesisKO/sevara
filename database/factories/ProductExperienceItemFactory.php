<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductExperienceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductExperienceItem>
 */
class ProductExperienceItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'icon_path' => 'products/experience-icons/'.fake()->uuid().'.png',
            'title' => fake()->words(2, true),
            'description' => fake()->sentence(6),
            'order' => 0,
        ];
    }
}
