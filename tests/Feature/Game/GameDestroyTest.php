<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('games.destroy redirects to login page for guest', function () {
    $game = Game::factory()
        ->for(User::factory()->create(['role' => UserRole::GAME_CREATOR]), 'creator')
        ->for(Genre::factory()->create())
        ->create();

    $response = $this->delete("/games/{$game->slug}");

    $response->assertRedirect(route('security.login'));
});

test('games.destroy passes only for owner or staff', function (string $actor, int $expectedStatus) {
    $owner = User::factory()->create(['role' => UserRole::GAME_CREATOR ]);
    $game = Game::factory()->for($owner, 'creator')->for(Genre::factory()->create())->create();

    $user = match($actor) {
        'owner' => $owner,
        'moderator' => User::factory()->create(['role' => UserRole::MODERATOR]),
        'admin' => User::factory()->create(['role' => UserRole::ADMIN]),
        'dev' => User::factory()->create(['role' => UserRole::DEVELOPER]),
        'creator' => User::factory()->create(['role' => UserRole::GAME_CREATOR]),
        'user' => User::factory()->create(['role' => UserRole::USER]),
    };

    $response = $this->actingAs($user)->delete("/games/{$game->slug}");

    $response->assertStatus($expectedStatus);
})->with([
    'owner can' => ['owner', 302],
    'moderator can' => ['moderator', 302],
    'admin can' => ['admin', 302],
    'dev can' => ['dev', 302],
    'non-owner creator cannot' => ['creator', 403],
    'regular user cannot' => ['user', 403],
]);

test('games.destroy deletes game and all affiliated files', function () {
    Storage::fake('public');
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $this->actingAs($user)->post("/games", validGamePayload(Genre::factory()->create()));

    $game = Game::query()->firstOrFail();

    $response = $this->actingAs($user)->delete("/games/{$game->slug}");

    $response->assertRedirect();

    $this->assertDatabaseCount('games', 0);

    Storage::disk('public')->assertMissing("games/{$game->slug}");
});
