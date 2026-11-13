<?php

namespace Database\Factories;

use App\Enum\ReviewKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
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
            'content' => fake()->sentences(5, true),
            'ratings' => [
                ReviewKey::GAMEPLAY->value => fake()->numberBetween(0, 5),
                ReviewKey::GRAPHICS->value => fake()->numberBetween(0, 5),
                ReviewKey::STORYLINE->value => fake()->numberBetween(0, 5),
                ReviewKey::REPLAYABILITY->value => fake()->numberBetween(0, 5),
                ReviewKey::SOUND_AND_MUSIC->value => fake()->numberBetween(0, 5),
                ReviewKey::PERFORMANCE->value => fake()->numberBetween(0, 5)
            ]
        ];
    }
}
