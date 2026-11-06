<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function validGameUpdatePayload(Game $game, Genre $genre, array $overrides = []): array
{
    return array_merge(
        validGamePayload($genre),
        [
            'media_to_delete' => [$game->media[0]]
        ],
        $overrides
    );
}

beforeEach(function () {
    Storage::fake('public');
});

test('games.store redirects to login page for guest', function () {
    $game = Game::factory()
        ->for(User::factory()->create(['role' => UserRole::GAME_CREATOR]), 'creator')
        ->for(Genre::factory()->create())
        ->create();

    $response = $this->post("/games/{$game->slug}");

    $response->assertRedirect(route('security.login'));
});

test('games.store passes only for owner or staff', function (string $actor, int $expectedStatus) {
    $genre = Genre::factory()->create();
    $owner = User::factory()->create(['role' =>UserRole::GAME_CREATOR ]);
    $game = Game::factory()->for($owner, 'creator')->for($genre)->create();

    $user = match($actor) {
        'owner' => $owner,
        'moderator' => User::factory()->create(['role' => UserRole::MODERATOR]),
        'admin' => User::factory()->create(['role' => UserRole::ADMIN]),
        'dev' => User::factory()->create(['role' => UserRole::DEVELOPER]),
        'creator' => User::factory()->create(['role' => UserRole::GAME_CREATOR]),
        'user' => User::factory()->create(['role' => UserRole::USER]),
    };

    $response = $this->actingAs($user)->post("/games/{$game->slug}", validGamePayload($genre));

    $response->assertStatus($expectedStatus);
})->with([
    'owner can' => ['owner', 302],
    'moderator can' => ['moderator', 302],
    'admin can' => ['admin', 302],
    'dev can' => ['dev', 302],
    'non-owner creator cannot' => ['creator', 403],
    'regular user cannot' => ['user', 403],
]);

test('games.update redirects with errors when mandatory data are missing', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $genre = Genre::factory()->create();
    $game = Game::factory()->for($user, 'creator')->for($genre)->create();

    $response = $this->actingAs($user)->post("/games/{$game->slug}");

    $response
        ->assertSessionHasErrors()
        ->assertRedirect();

    $this->assertDatabaseHas('games', [
        'slug' => $game->slug,
        'title' => $game->title,
    ]);
});

test('games.update will redirect with errors on invalid data', function (string $field, mixed $value) {
    $genre = Genre::factory()->create();
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);

    $this->actingAs($user)->post('/games', validGamePayload($genre));

    $game = Game::query()->firstOrFail();

    $response = $this
        ->actingAs($user)
        ->post("/games/{$game->slug}", validGameUpdatePayload($game, Genre::factory()->create(), [$field => $value]));

    $response
        ->assertSessionHasErrors($field)
        ->assertRedirect();

    $game->refresh();

    $this->assertDatabaseHas('games', [
        'slug' => $game->slug,
        'title' => $game->title,
        'description' => $game->description,
        'genre_id' => $genre->id,
        'released_at' => $game->released_at->format("Y-m-d")
    ]);
})->with([
    'empty title' => ['title', ''],
    'description too short' => ['description', 'short'],
    'bad date format' => ['released_at', 'not-a-date'],
    'unknown genre' => ['genre', 'does-not-exist'],
    'too many media' => ['media', 99],
    'wrong media format' => ['media', UploadedFile::fake()->create('wrong.txt', 204, 'text/plain')],
    'too many media to delete' => ['media_to_delete', ['image-1.png','image-2.png','image-3.png','image-4.png','image-5.png', 'image-6.png', 'image-7.png', 'image-8.png']],
]);

test('games.update will update entry with new data', function () {
    $genre = Genre::factory()->create();
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);

    $this->actingAs($user)->post('/games', validGamePayload($genre));

    $game = Game::query()->firstOrFail();
    $newGenre = Genre::factory()->create();

    $payload = validGameUpdatePayload($game, $newGenre);

    $response = $this->actingAs($user)->post("/games/{$game->slug}", $payload);

    $response->assertRedirect();

    $game->refresh();

    $this->assertDatabaseHas('games', [
        'slug' => $game->slug,
        'title' => $payload['title'],
        'description' => $payload['description'],
        'genre_id' => $newGenre->id,
        'released_at' => $payload['released_at'],
        'media' => json_encode(array_values(array_filter($game->media, fn ($entry) => "{$entry}" !== $payload['media_to_delete'][0]))),
    ]);
});
