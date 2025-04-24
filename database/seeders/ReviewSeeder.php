<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$games = Game::select('id')->get();
		$users = User::select('id')->get();

		for ($i = 0; $i < 5000; $i++) {
			Review::factory()
			->for(collect($games)->random())
			->for(collect($users)->random())
			->create();
		}
	}
}
