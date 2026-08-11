<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductSpecification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductSpecification>
 */
class ProductSpecificationFactory extends Factory
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
            'icon_path' => 'products/spec-icons/'.fake()->uuid().'.png',
            'question' => fake()->sentence(3),
            'answer' => fake()->sentence(12),
            'order' => 0,
        ];
    }
}
