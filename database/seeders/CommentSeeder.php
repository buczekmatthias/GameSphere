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
        $discussions = Discussion::select('id')->get();

        Comment::factory(500)
            ->state(fn () => [
                'user_id' => $users->random(),
                'discussion_id' => $discussions->random()
            ])
            ->create();
    }
}
