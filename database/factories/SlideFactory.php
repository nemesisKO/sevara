<?php

namespace Database\Factories;

use App\Models\Slide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Slide>
 */
class SlideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image_path' => 'slides/'.fake()->uuid().'.jpg',
            'link' => fake()->url(),
            'primary_cta_text' => fake()->words(2, true),
            'primary_cta_link' => fake()->url(),
            'secondary_cta_text' => fake()->words(2, true),
            'secondary_cta_link' => fake()->url(),
        ];
    }
}
