<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $part1 = fake()->word();
        $part2 = fake()->words(2, true);

        return [
            'slug' => (string) str($part1.'-'.$part2)->slug(),
            'title_part1' => ucfirst($part1),
            'title_part1_color' => '#a83b52',
            'title_part2' => ucfirst($part2),
            'title_part2_color' => '#0f1c3f',
            'subtitle' => fake()->sentence(3),
            'subtitle_color' => '#a83b52',
            'description' => fake()->sentence(10),
        ];
    }
}
