<?php

namespace Database\Factories;

use App\Models\PackagingFormat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackagingFormat>
 */
class PackagingFormatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(8),
            'image_path' => 'packaging-formats/'.fake()->uuid().'.jpg',
            'is_featured' => true,
            'order' => 0,
        ];
    }

    public function other(): static
    {
        return $this->state(fn (): array => ['is_featured' => false]);
    }
}
