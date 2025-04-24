<?php

namespace Database\Seeders;

use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$users = User::select('id')->get();

		for ($i = 0; $i < rand(15, 50); $i++) {
			Genre::factory()
			->afterCreating(function (Genre $genre) use ($users) {
				for ($i = 0; $i < rand(50, 300); $i++) {
					Discussion::factory()
					->for($genre, 'discussable')
					->for(collect($users)->random(), 'author')
					->create();
				}
			})
			->create();
		}
	}
}
