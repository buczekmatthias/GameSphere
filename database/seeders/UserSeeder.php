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
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'username' => 'admin_account',
            'role' => UserRole::DEVELOPER
        ]);

        User::factory(10)->create(['role' => UserRole::ADMIN]);
        User::factory(25)->create(['role' => UserRole::MODERATOR]);
        User::factory(500)->create(['role' => UserRole::GAME_CREATOR]);
        User::factory(1000)->create(['role' => UserRole::USER]);
    }
}
