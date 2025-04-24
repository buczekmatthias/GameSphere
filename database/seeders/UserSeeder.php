<?php

namespace Database\Seeders;

use App\Enum\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		User::factory()->create([
			'name' => 'Hazy',
			'email' => 'hazy@test.com',
			'username' => '_hazy',
			'role' => UserRole::DEVELOPER->value
		]);

		User::factory(1000)->create();

		$users = User::where('role', UserRole::USER->value)->inRandomOrder()->limit(15)->get();

		foreach ($users as $u) {
			$u->role = UserRole::GAME_CREATOR->value;
			$u->save();
		}
	}
}
