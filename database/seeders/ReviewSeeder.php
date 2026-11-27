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

        Review::factory(5000)
            ->state(fn () => [
                'game_id' => $games->random(),
                'user_id' => $users->random()
            ])
            ->create();
    }
}
