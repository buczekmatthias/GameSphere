<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function validGamePayload(Genre $genre, array $overrides = []): array
{
    return array_merge([
        'title'       => 'Example title',
        'description' => str_repeat('a', 50),
        'thumbnail'   => UploadedFile::fake()->image('thumbnail.png'),
        'released_at' => now()->format('Y-m-d'),
        'genre'       => $genre->slug,
    ], $overrides);
}


test('games.store redirects to login page for guest', function () {
    $genre = Genre::factory()->create();
    Storage::fake('public');

    $response = $this->post('/games', validGamePayload($genre));

    $response->assertRedirect(route('security.login'));
});

test('games.store fails for regular user', function () {
    $genre = Genre::factory()->create();
    Storage::fake('public');

    $response = $this->actingAs(User::factory()->create(['role' => UserRole::USER]))->post('/games', validGamePayload($genre));

    $response->assertForbidden();

    $this->assertDatabaseCount('games', 0);
});

test('any authorized user with role different than USER can access games.store and create game when request is valid', function (UserRole $role) {
    $genre = Genre::factory()->create();
    $user = User::factory()->create(['role' => $role]);
    Storage::fake('public');
    $payload = validGamePayload($genre);

    $response = $this->actingAs($user)->post('/games', $payload);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $game = Game::where('title', $payload['title'])->firstOrFail();

    expect($game->title)->toBe($payload['title'])
    ->and($game->description)->toBe($payload['description'])
    ->and($game->released_at->format('Y-m-d'))->toBe($payload['released_at'])
    ->and($game->genre_id)->toBe($genre->id)
    ->and($game->user_id)->toBe($user->id);


    Storage::disk('public')->assertExists("games/{$game->slug}/{$game->thumbnail}");
})->with([
    'game creator' => UserRole::GAME_CREATOR,
    'moderator' => UserRole::MODERATOR,
    'admin' => UserRole::ADMIN,
    'developer' => UserRole::DEVELOPER,
]);

test('games.store redirects with errors when mandatory data are missing', function () {
    $response = $this->actingAs(User::factory()->create())->post('/games');

    $response
        ->assertSessionHasErrors()
        ->assertRedirect();

    $this->assertDatabaseCount('games', 0);
});

test('games.store will redirect with errors on invalid data', function (string $field, mixed $value) {
    $genre = Genre::factory()->create();
    Storage::fake('public');
    $date = now()->format('Y-m-d');

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::GAME_CREATOR]))
        ->post('/games', validGamePayload($genre, [$field => $value]));

    $response
        ->assertSessionHasErrors($field)
        ->assertRedirect();

    $this->assertDatabaseCount('games', 0);
})->with([
    'empty title' => ['title', ''],
    'description too short' => ['description', 'short'],
    'bad date format' => ['released_at', 'not-a-date'],
    'unknown genre' => ['genre', 'does-not-exist'],
]);
