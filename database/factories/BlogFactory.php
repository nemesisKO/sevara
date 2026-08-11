<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'blog_category_id' => BlogCategory::factory(),
            'title' => rtrim($title, '.'),
            'slug' => (string) str($title)->slug(),
            'author' => fake()->name(),
            'source_link' => null,
            'custom_date' => fake()->monthName().' '.fake()->year(),
            'read_duration' => (string) fake()->numberBetween(3, 12),
            'mini_review' => fake()->sentence(12),
            'meta_description' => fake()->sentence(15),
            'image_path' => null,
            'references' => [],
            'faqs' => [],
        ];
    }
}
