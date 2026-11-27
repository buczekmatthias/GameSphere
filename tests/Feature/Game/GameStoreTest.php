<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function validGameStorePayload(array $overrides = []): array
{
    return array_merge(
        [
            'title' => 'Example title',
            'description' => str_repeat('a', 50),
            'thumbnail' => UploadedFile::fake()->image('thumbnail.png'),
            'media' => [
                UploadedFile::fake()->image('image-1.png'),
                UploadedFile::fake()->image('image-2.png'),
                UploadedFile::fake()->image('image-3.png'),
            ],
            'released_at' => now()->format('Y-m-d'),
            'genre' => Genre::factory()->create()->slug,
        ],
        $overrides
    );
}

test('games.store redirects to login page for guest', function () {
    $this
        ->post(route("games.store"), validGameStorePayload())
        ->assertRedirect(route('security.login'));
});

test('games.store fails for regular user', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::USER]))
        ->post(route("games.store"), validGameStorePayload())
        ->assertForbidden();

    $this->assertDatabaseCount('games', 0);
});

test('any authorized user with role different than USER can access games.store and create game when request is valid', function (UserRole $role) {
    Storage::fake('public');
    $user = User::factory()->create(['role' => $role]);
    $payload = validGameStorePayload();

    $this
        ->actingAs($user)
        ->post(route("games.store"), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $game = Game::query()->firstOrFail();

    expect($game->title)->toBe($payload['title'])
        ->and($game->description)->toBe($payload['description'])
        ->and($game->released_at->format('Y-m-d'))->toBe($payload['released_at'])
        ->and($game->user_id)->toBe($user->id);


    Storage::disk('public')->assertExists("games/{$game->slug}/{$game->thumbnail}");
    foreach ($game->media as $media) {
        Storage::disk('public')->assertExists("games/{$game->slug}/{$media}");
    }
})->with([
    'game creator' => UserRole::GAME_CREATOR,
    'moderator' => UserRole::MODERATOR,
    'admin' => UserRole::ADMIN,
    'developer' => UserRole::DEVELOPER,
]);

test('games.store redirects with errors when mandatory data are missing', function () {
    $this
        ->actingAs(User::factory()->create())
        ->post(route("games.store"))
        ->assertSessionHasErrors()
        ->assertRedirect();

    $this->assertDatabaseCount('games', 0);
});

test('games.store will redirect with errors on invalid data', function (string $field, mixed $value) {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::GAME_CREATOR]))
        ->post(route("games.store"), validGameStorePayload([$field => $value]))
        ->assertSessionHasErrors($field)
        ->assertRedirect();

    $this->assertDatabaseCount('games', 0);
})->with([
    'empty title' => ['title', ''],
    'description too short' => ['description', 'short'],
    'bad date format' => ['released_at', 'not-a-date'],
    'unknown genre' => ['genre', 'does-not-exist'],
]);
