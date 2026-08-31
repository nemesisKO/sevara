<?php

namespace Database\Factories;

use App\Models\PrivateLabelInquiry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrivateLabelInquiry>
 */
class PrivateLabelInquiryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'company_name' => fake()->company(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'intent' => fake()->randomElement(PrivateLabelInquiry::INTENTS),
            'country' => fake()->randomElement(PrivateLabelInquiry::MARKETS),
            'product_format' => fake()->words(2, true),
            'product_name' => fake()->words(3, true),
            'estimated_quantity' => fake()->numberBetween(1, 50).',000 units',
            'message' => fake()->sentence(12),
            'read_at' => null,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (): array => ['read_at' => now()]);
    }
}
