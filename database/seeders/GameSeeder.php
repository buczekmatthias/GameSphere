<?php

namespace Database\Seeders;

use App\Enum\UserRole;
use App\Models\Discussion;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$genres = Genre::select('id')->get();
		$creators = User::select('id')->where('role', UserRole::GAME_CREATOR->value)->get();
		$users = User::select('id')->get();

		for ($i = 0; $i < 200; $i++) {
			Game::factory()
			->for(collect($genres)->random())
			->for(collect($creators)->random(), 'creator')
			->afterCreating(function (Game $game) use ($users) {
				for ($i = 0; $i < rand(50, 300); $i++) {
					Discussion::factory()
					->for($game, 'discussable')
					->for(collect($users)->random(), 'author')
					->create();
				}
			})
			->create();
		}
	}
}
