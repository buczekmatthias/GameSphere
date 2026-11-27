<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Str;

test('games.edit redirects to login page for guest', function () {
    $this
        ->get(route("games.edit", ['game' => Game::factory()->create()]))
        ->assertRedirect(route('security.login'));
});

test('games.edit passes only for owner or staff', function (UserRole|string $role, int $expectedStatus) {
    $game = Game::factory()->create();

    $user = match($role) {
        'owner' => $game->creator,
        default => User::factory()->create(['role' => $role])
    };

    $this
        ->actingAs($user)
        ->get(route("games.edit", ['game' => $game]))
        ->assertStatus($expectedStatus);
})->with([
    'owner' => ['owner', 200],
    'user' => [UserRole::USER, 403],
    'creator' => [UserRole::GAME_CREATOR, 403],
    'mod' => [UserRole::MODERATOR, 200],
    'admin' => [UserRole::ADMIN, 200],
    'dev' => [UserRole::DEVELOPER, 200],
]);

test('games.edit provides inertia response with game and list of genres', function () {
    $genres = Genre::factory(5)->create();
    $game = Game::factory()->for($genres->first())->create();

    $this
        ->actingAs($game->creator)
        ->get(route("games.edit", ['game' => $game]))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Edit')
                ->has('game')
                ->has('genres', 5)
                ->where('game.slug', $game->slug)
                ->where('game.title', $game->title)
        );
});

test('games.edit will return 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("games.edit", ['game' => Str::uuid()]))
        ->assertNotFound();
});
