<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\SampleRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SampleRequest>
 */
class SampleRequestFactory extends Factory
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
            'product_name' => 'Chocolate Dates',
            'full_name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'company_name' => fake()->company(),
            'phone' => fake()->phoneNumber(),
            'sample_volume' => fake()->randomElement(SampleRequest::VOLUMES),
            'shipping_term' => fake()->randomElement(SampleRequest::SHIPPING_TERMS),
            'description' => fake()->sentence(),
        ];
    }
}
