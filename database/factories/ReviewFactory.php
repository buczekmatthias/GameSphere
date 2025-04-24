<?php

namespace Database\Factories;

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
			'ratings' => json_encode([
				'gameplay' => fake()->numberBetween(0, 5),
				'graphics' => fake()->numberBetween(0, 5),
				'storyline' => fake()->numberBetween(0, 5),
				'replayability' => fake()->numberBetween(0, 5),
				'sound_and_music' => fake()->numberBetween(0, 5),
				'performance' => fake()->numberBetween(0, 5)
			])
		];
	}
}
