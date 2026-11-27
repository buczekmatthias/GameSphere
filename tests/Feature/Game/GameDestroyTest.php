<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('games.destroy redirects to login page for guest', function () {
    $this
        ->delete(route("games.destroy", ['game' => Game::factory()->create()]))
        ->assertRedirect(route('security.login'));
});

test('games.destroy passes only for owner or staff', function (UserRole|string $role, int $expectedStatus, int $expectedCount) {
    $game = Game::factory()->create();

    $user = match($role) {
        'owner' => $game->creator,
        default => User::factory()->create(['role' => $role])
    };

    $this
        ->actingAs($user)
        ->delete(route("games.destroy", ['game' => $game]))
        ->assertStatus($expectedStatus);

    $this->assertDatabaseCount('games', $expectedCount);
})->with([
    'owner' => ['owner', 302, 0],
    'user' => [UserRole::USER, 403, 1],
    'creator' => [UserRole::GAME_CREATOR, 403, 1],
    'mod' => [UserRole::MODERATOR, 302, 0],
    'admin' => [UserRole::ADMIN, 302, 0],
    'dev' => [UserRole::DEVELOPER, 302, 0],
]);

test('games.destroy deletes game and all affiliated files', function () {
    Storage::fake('public');

    $slug = Str::orderedUuid();
    $file = putFakeFile("games/{$slug}");
    $game = Game::factory()->create(['slug' => $slug, 'media' => [$file]]);

    $this
        ->actingAs($game->creator)
        ->delete(route("games.destroy", ['game' => $game]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('games', 0);

    Storage::disk('public')->assertMissing("games/{$game->slug}");
    Storage::disk('public')->assertMissing("games/{$game->slug}/{$file}");
});

test('games.destroy will return 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->delete(route("games.destroy", ['game' => Str::uuid()]))
        ->assertNotFound();
});
