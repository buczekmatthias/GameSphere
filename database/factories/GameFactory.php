<?php

namespace Database\Factories;

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
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
            'title' => fake()->unique()->word(),
            'description' => fake()->sentences(6, true),
            'thumbnail' => 'fake.png',
            'released_at' => fake()->dateTimeBetween('-10 years', '+5 years'),
            'genre_id' => Genre::factory(),
            'user_id' => User::factory()->state(['role' => UserRole::GAME_CREATOR])
        ];
    }
}
