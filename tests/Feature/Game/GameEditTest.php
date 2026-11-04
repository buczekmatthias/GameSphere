<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('games.edit redirects to login page for guest', function () {
    $genre = Genre::factory()->create();
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $game = Game::factory()->for($genre)->for($user, 'creator')->create();

    $response = $this->get("/games/{$game->slug}/edit");

    $response->assertRedirect(route('security.login'));
});

test('games.edit passes only for owner or staff', function (string $actor, int $expectedStatus) {
    $genre = Genre::factory()->create();
    $owner = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $game = Game::factory()->for($genre)->for($owner, 'creator')->create();

    $user = match($actor) {
        'owner'      => $owner,
        'moderator'  => User::factory()->create(['role' => UserRole::MODERATOR]),
        'admin'      => User::factory()->create(['role' => UserRole::ADMIN]),
        'dev'      => User::factory()->create(['role' => UserRole::DEVELOPER]),
        'creator' => User::factory()->create(['role' => UserRole::GAME_CREATOR]),
        'user' => User::factory()->create(['role' => UserRole::USER]),
    };

    $response = $this->actingAs($user)->get("/games/{$game->slug}/edit");

    $response->assertStatus($expectedStatus);
})->with([
    'owner can'      => ['owner', 200],
    'moderator can'  => ['moderator', 200],
    'admin can'      => ['admin', 200],
    'dev can'      => ['dev', 200],
    'non-owner creator cannot' => ['creator', 403],
    'regular user cannot' => ['user', 403],
]);

test('games.edit provides inertia response with game and list of genres', function () {
    $genres = Genre::factory(5)->create();
    $owner = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $game = Game::factory()->for($genres->first())->for($owner, 'creator')->create();

    $response = $this->actingAs($owner)->get("/games/{$game->slug}/edit");

    $response
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
