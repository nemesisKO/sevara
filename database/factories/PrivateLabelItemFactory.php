<?php

namespace Database\Factories;

use App\Enums\PrivateLabelSection;
use App\Models\PrivateLabelItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PrivateLabelItem>
 */
class PrivateLabelItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'section' => PrivateLabelSection::Highlight,
            'icon_path' => 'private-label/icons/'.fake()->uuid().'.svg',
            'title' => fake()->words(2, true),
            'description' => fake()->sentence(8),
            'order' => 0,
        ];
    }

    public function section(PrivateLabelSection $section): static
    {
        return $this->state(fn (): array => ['section' => $section]);
    }
}
