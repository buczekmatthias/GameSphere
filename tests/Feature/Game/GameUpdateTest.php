<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function validGameUpdatePayload(array $overrides = []): array
{
    return array_merge(
        [
            'title' => 'New title',
            'description' => str_repeat('b', 50),
            'media' => [
                UploadedFile::fake()->image("image.png"),
                UploadedFile::fake()->image("image2.png"),
            ],
        ],
        $overrides
    );
}

test('games.update redirects to login page for guest', function () {
    $this
        ->post(route("games.update", ['game' => Game::factory()->create()]))
        ->assertRedirect(route('security.login'));
});

test('games.update passes only for owner or staff', function (UserRole|string $role, int $expectedStatus) {
    Storage::fake('public');

    $slug = Str::orderedUuid();
    $thumbnail = putFakeFile("games/{$slug}");
    $file = putFakeFile("games/{$slug}");
    $game = Game::factory()->create(['slug' => $slug, 'media' => [$file], 'thumbnail' => $thumbnail]);

    $user = match($role) {
        'owner' => $game->creator,
        default => User::factory()->create(['role' => $role]),
    };

    $this
        ->actingAs($user)
        ->post(route("games.update", ['game' => $game]), validGameUpdatePayload())
        ->assertStatus($expectedStatus);
})->with([
    'owner' => ['owner', 302],
    'user' => [UserRole::USER, 403],
    'creator' => [UserRole::GAME_CREATOR, 403],
    'mod' => [UserRole::MODERATOR, 302],
    'admin' => [UserRole::ADMIN, 302],
    'dev' => [UserRole::DEVELOPER, 302],
]);

test('games.update redirects with errors when mandatory data are missing', function () {
    $game = Game::factory()->create();

    $this
        ->actingAs($game->creator)
        ->post(route("games.update", ['game' => $game]))
        ->assertSessionHasErrors()
        ->assertRedirect();

    $this->assertDatabaseHas('games', [
        'slug' => $game->slug,
        'title' => $game->title,
    ]);
});

test('games.update will redirect with errors on invalid data', function (string $field, mixed $value) {
    Storage::fake('public');

    $slug = Str::orderedUuid();
    $thumbnail = putFakeFile("games/{$slug}");
    $file = putFakeFile("games/{$slug}");
    $game = Game::factory()->create(['slug' => $slug, 'media' => [$file], 'thumbnail' => $thumbnail]);
    $g = $game->genre;

    $this
        ->actingAs($game->creator)
        ->post(route("games.update", ['game' => $game]), validGameUpdatePayload([$field => $value]))
        ->assertSessionHasErrors($field)
        ->assertRedirect();

    $game->refresh();

    $this->assertDatabaseHas('games', [
        'slug' => $game->slug,
        'title' => $game->title,
        'description' => $game->description,
        'genre_id' => $g->id,
        'released_at' => $game->released_at
    ]);

    expect(count($game->media))->toBe(1);

    Storage::disk('public')->assertExists("games/{$slug}/{$thumbnail}");
    Storage::disk('public')->assertExists("games/{$slug}/{$file}");
    Storage::disk('public')->assertCount("games/{$slug}", 2);
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
    Storage::fake('public');

    $slug = Str::orderedUuid();
    $thumbnail = putFakeFile("games/{$slug}");
    $file = putFakeFile("games/{$slug}");
    $game = Game::factory()->create(['slug' => $slug, 'media' => [$file], 'thumbnail' => $thumbnail, 'title' => 'Example title']);

    $newGenre = Genre::factory()->create();

    $payload = validGameUpdatePayload(['genre' => $newGenre->slug, 'media' => [], 'media_to_delete' => [$file]]);

    $original = $game->media;

    $this
        ->actingAs($game->creator)
        ->post(route("games.update", ['game' => $game]), $payload)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $game->refresh();

    $this->assertDatabaseHas('games', [
        'slug' => $game->slug,
        'title' => $payload['title'],
        'description' => $payload['description'],
        'genre_id' => $newGenre->id,
    ]);

    $kept = array_values(array_diff($original, $payload['media_to_delete']));

    expect($game->media)
        ->toHaveCount(count($kept) + count($payload['media']))
        ->and(array_slice($game->media, 0, count($kept)))->toBe($kept)
        ->and($game->media)->toBe(array_values(array_unique($game->media)));

    Storage::disk('public')->assertMissing("games/{$game->slug}/{$file}");
    Storage::disk('public')->assertCount("games/{$game->slug}", 1);
});

test('games.update will return 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create())
        ->post(route("games.update", ['game' => Str::uuid()]), validGameUpdatePayload())
        ->assertNotFound();
});
