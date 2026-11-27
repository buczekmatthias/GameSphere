<?php

use App\Models\Game;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('reviews.create redirects for guest', function () {
    $this
        ->get(route("reviews.create", ['game' => Game::factory()->create()]))
        ->assertRedirect(route('security.login'));
});

test('reviews.create returns inertia response with game content', function () {
    $game = Game::factory()->create();

    $this
        ->actingAs(User::factory()->create())
        ->get(route("reviews.create", ['game' => $game]))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/review/Create')
                ->has('game')
                ->where('game.slug', $game->slug)
                ->where('game.title', $game->title)
        );
});
