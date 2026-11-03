<?php

use App\Enum\GameCollectionType;
use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia;

test('anyone can visit homepage', function () {
    $guestResponse = $this->get('/');
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('homepage displays only newest games (before end of today)', function () {
    User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    Genre::factory()->create();
    Game::factory()->create(['released_at' => now()->startOfDay()->subDay()]);
    Game::factory()->create(['released_at' => now()->endOfWeek()]);

    $response = $this->get("/newest");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->has('activeType')
                ->has('types')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games', 1)
                        ->where(
                            'games.0.released_at',
                            fn (string $released_at) => Carbon::parse($released_at)->isBefore(now()->endOfDay())
                        )
                )
        );
});

test('homepage displays only most popular games (ones that were marked as favorite by users)', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    Genre::factory()->create();
    $game = Game::factory()->create(['released_at' => now()->startOfWeek()->subDay()]);
    Game::factory()->create(['released_at' => now()->startOfMonth()]);

    $user->games()->attach($game, ['list_type' => GameCollectionType::FAVORITE->value]);

    $response = $this->get("/most_popular");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->has('activeType')
                ->has('types')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games', 1)
                        ->where(
                            'games',
                            fn (Collection $games) => $games->first()['favoriteUsers'] > 0
                        )
                )
        );
});

test('homepage displays only upcoming games (between tomorrow and end of next year)', function () {
    User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    Genre::factory()->create();
    Game::factory()->create(['released_at' => now()->startOfWeek()->subDay()]);
    Game::factory()->create(['released_at' => now()->endOfWeek()->addDay()]);
    Game::factory()->create(['released_at' => now()->addMonths(8)]);


    $response = $this->get("/upcoming");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->has('activeType')
                ->has('types')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games', 2)
                        ->where(
                            'games.0.released_at',
                            function (string $released_at) {
                                $parsed = Carbon::parse($released_at);
                                return $parsed->isAfter(now()->endOfDay()) && $parsed->isBefore(now()->endOfYear()->addYear()->addDay());
                            }
                        )
                        ->where(
                            'games.1.released_at',
                            function (string $released_at) {
                                $parsed = Carbon::parse($released_at);
                                return $parsed->isAfter(now()->endOfDay()) && $parsed->isBefore(now()->endOfYear()->addYear()->addDay());
                            }
                        )
                )
        );
});
