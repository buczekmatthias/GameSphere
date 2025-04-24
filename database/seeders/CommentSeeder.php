<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$users = User::select('id')->get();
		$discussions = Discussion::inRandomOrder()->select('id')->limit(100)->get();

		foreach ($discussions as $d) {
			for ($i = 0; $i < rand(10, 100); $i++) {
				$c = Comment::factory()->for($d)->make();

				$c->user()->associate(collect($users)->random());
				$c->save();
			}
		}
	}
}
