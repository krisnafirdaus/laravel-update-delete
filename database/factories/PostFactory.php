<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();
        $status = fake()->randomElement(['draft', 'published']);

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(1, true),
            'status' => $status,
            'published_at' => $status === 'published' ? fake()->dateTimeBetween('-1 month', 'now') : null
        ];
    }

    // state published
    public function published(): static
    {
        return $this->state(fn (array $attributres) => [
            'status' => 'published',
            'published_at' => fake()->dateTimeBetween('-1 month', 'now')
        ]);
    }

    // state draft
    public function draft(): static
    {
        return $this->state(fn (array $attributres) =>[
            'status' => 'draft',
            'published_at' => null
        ]);
    }
}
