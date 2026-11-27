<?php

use App\Enum\GameCollectionType;
use App\Enum\UserRole;
use App\Models\Game;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Str;

test('anyone can visit games.show', function () {
    $game = Game::factory()->create();

    $this
        ->get(route("games.show", ['game' => $game]))
        ->assertStatus(200);

    $this
        ->actingAs(User::factory()->create())
        ->get(route("games.show", ['game' => $game]))
        ->assertStatus(200);
});

test('games.show returns inertia respose with game, active tab and lists user has added game to for authenticated user', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $game = Game::factory()
        ->for($user, 'creator')
        ->create();
    $user->games()->attach($game, ['list_type' => GameCollectionType::FAVORITE->value]);
    
    $this
        ->actingAs($user)
        ->get(route("games.show", ['game' => $game]))
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
    $game = Game::factory()->create();
    
    $this
        ->get(route("games.show", ['game' => $game]))
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

test('games.show returns 404 for missing entry', function () {
    $this
        ->get(route("games.show", ['game' => Str::uuid()]))
        ->assertNotFound();
});
