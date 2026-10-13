<?php

namespace Database\Factories;

use App\Models\PrivateLabelProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrivateLabelProject>
 */
class PrivateLabelProjectFactory extends Factory
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
            'details' => [
                'project_type' => 'Launching a new brand',
                'product_interest' => fake()->sentence(),
            ],
            'files' => [],
            'consented_at' => now(),
        ];
    }
}
