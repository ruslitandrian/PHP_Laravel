<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(rand(3, 6));
        
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(rand(3, 10), true),
            'excerpt' => $this->faker->sentence(rand(10, 20)),
            'featured_image' => 'https://picsum.photos/800/400?random=' . rand(1, 1000),
            'author_id' => User::factory(),
            'is_active' => $this->faker->boolean(70), 
            'order' => $this->faker->numberBetween(0, 100),
            'meta_title' => $this->faker->sentence(rand(3, 6)),
            'meta_description' => $this->faker->sentence(rand(8, 12)),
            'meta_keywords' => implode(', ', $this->faker->words(rand(3, 6))),
            'views' => $this->faker->numberBetween(0, 1000),
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}