<?php

use App\Enum\GameCollectionType;
use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('anyone can visit games.show', function () {
    $genre = Genre::factory()->create();
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $game = Game::factory()->for($genre)->for($user, 'creator')->create();

    $guestResponse = $this->get("/games/{$game->slug}");
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/games/{$game->slug}");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('games.show returns inertia respose with game, active tab and lists user has added game to for authenticated user', function () {
    $genre = Genre::factory()->create();
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $game = Game::factory()->for($genre)->for($user, 'creator')->create();
    $user->games()->attach($game, ['list_type' => GameCollectionType::FAVORITE->value]);
    
    $response = $this->actingAs($user)->get("/games/{$game->slug}");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Show')
                ->has('game')
                ->where('game.slug', $game->slug)
                ->where('game.title', $game->title)
                ->has('tab')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('userLists')
                        ->whereNot('userLists', null)
                )
        );
});

test('games.show returns inertia respose with game, active tab and null as lists user has added game to for guest user', function () {
    $genre = Genre::factory()->create();
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $game = Game::factory()->for($genre)->for($user, 'creator')->create();
    
    $response = $this->get("/games/{$game->slug}");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Show')
                ->has('game')
                ->where('game.slug', $game->slug)
                ->where('game.title', $game->title)
                ->has('tab')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('userLists')
                        ->where('userLists', null)
                )
        );
});
