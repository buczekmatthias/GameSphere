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
        Genre::factory(50)->create();
    }
}
