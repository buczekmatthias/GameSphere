<?php

namespace Database\Factories;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discussion>
 */
class DiscussionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->uuid(),
            'title' => fake()->sentence(),
            'is_locked' => false,
            'user_id' => User::factory(),
            'discussable_type' => "App\\Models\\Genre",
            'discussable_id' => Genre::factory()
        ];
    }
}
