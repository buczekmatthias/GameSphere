<?php

namespace Database\Seeders;

use App\Models\Discussion;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class DiscussionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::select('id')->get();
        $games = Game::select('id')->get();
        $genres = Genre::select('id')->get();

        Discussion::factory(200)
            ->state(fn () => [
                'user_id' => $users->random(),
                ...match (fake()->boolean()) {
                    true => [
                        'discussable_type' => "App\\Models\\Game",
                        "discussable_id" => $games->random()
                    ],
                    false => [
                        'discussable_type' => "App\\Models\\Genre",
                        "discussable_id" => $genres->random()
                    ],
                }
            ])
            ->create();
    }
}
