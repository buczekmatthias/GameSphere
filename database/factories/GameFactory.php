<?php

namespace Database\Factories;

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
			'title' => fake()->word(),
			'description' => fake()->sentences(6, true),
			'thumbnail' => '',
			'released_at' => fake()->dateTimeBetween('-10 years', '+5 years')
		];
	}
}
