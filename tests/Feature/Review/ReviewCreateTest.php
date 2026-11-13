<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->game = Game::factory()
        ->for(User::factory()->create(['role' => UserRole::GAME_CREATOR]), 'creator')
        ->create();
});

test('reviews.create redirects for guest', function () {
    $response = $this->get("/games/{$this->game->slug}/reviews/create");

    $response->assertRedirect(route('security.login'));
});

test('reviews.create returns inertia response with game content', function () {
    $response = $this->actingAs(User::factory()->create())->get("/games/{$this->game->slug}/reviews/create");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/review/Create')
                ->has('game')
                ->where('game.slug', $this->game->slug)
                ->where('game.title', $this->game->title)
        );
});
