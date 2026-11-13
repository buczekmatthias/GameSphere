<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\UploadedFile;

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

function validGamePayload(Genre $genre, array $overrides = []): array
{
    return array_merge([
        'title'       => 'Example title',
        'description' => str_repeat('a', 50),
        'thumbnail'   => UploadedFile::fake()->image('thumbnail.png'),
        'media' => [
            UploadedFile::fake()->image('image-1.png'),
            UploadedFile::fake()->image('image-2.png'),
            UploadedFile::fake()->image('image-3.png'),
        ],
        'released_at' => now()->format('Y-m-d'),
        'genre'       => $genre->slug,
    ], $overrides);
}

function getReview(?User $user = null): Review
{
    $user = $user ?: User::factory()->create(['role' => UserRole::GAME_CREATOR]);

    return Review::factory()
        ->for(Game::factory()->for($user, 'creator')->create())
        ->for($user)
        ->create();
}
