<?php

namespace Database\Seeders;

use App\Enum\UserRole;
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
        $creators = User::select('id')->where('role', UserRole::GAME_CREATOR)->get();

        Game::factory()
            ->count(100)
            ->state(fn () => [
                'genre_id' => $genres->random(),
                'user_id'  => $creators->random(),
            ])
            ->create();
    }
}
