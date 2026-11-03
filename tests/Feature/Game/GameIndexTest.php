<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('anyone can visit games.index', function () {
    $guestResponse = $this->get('/games');
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/games");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('games.index displays games and provides genres', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR->value]);
    $genres = Genre::factory(3)->create();
    Game::factory(5)
        ->for($user, 'creator')
        ->for($genres->random())
        ->create();

    $response = $this->get("/games");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games.data', 5)
                        ->has('genres', 3)
                )
        );
});

test('games.index displays only games that match title query', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR->value]);
    $genre = Genre::factory()->create();
    Game::factory()
        ->for($user, 'creator')
        ->for($genre)
        ->create([
            'title' => 'First title'
        ]);
    Game::factory()
        ->for($user, 'creator')
        ->for($genre)
        ->create([
            'title' => 'Second title'
        ]);

    $response = $this->get("/games?title=first");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games.data', 1)
                )
        );
});

test('games.index displays only games that match genre query (case-insensitive)', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR->value]);
    $genre1 = Genre::factory()->create(['name' => 'First']);
    $genre2 = Genre::factory()->create(['name' => 'Second']);
    Game::factory()
        ->for($user, 'creator')
        ->for($genre1)
        ->create();
    Game::factory()
        ->for($user, 'creator')
        ->for($genre2)
        ->create();

    $response = $this->get("/games?genre=first");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games.data', 1)
                )
        );
});

test('games.index displays only games that was released within date range', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR->value]);
    $genre = Genre::factory()->create();
    $game = Game::factory()
        ->for($user, 'creator')
        ->for($genre)
        ->create(['released_at' => now()]);
    Game::factory()
        ->for($user, 'creator')
        ->for($genre)
        ->create(['released_at' => now()->subYears(5)]);

    $rangeStart = now()->subMonth()->format('Y-m-d');
    $rangeEnd = now()->addMonth()->format('Y-m-d');

    $response = $this->get("/games?released_after=$rangeStart&released_before=$rangeEnd");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games.data', 1)
                        ->where('games.data.0.slug', $game->slug)
                )
        );
});

test('games.index displays only games that was released after date', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR->value]);
    $genre = Genre::factory()->create();
    $game = Game::factory()
        ->for($user, 'creator')
        ->for($genre)
        ->create(['released_at' => now()]);
    Game::factory()
        ->for($user, 'creator')
        ->for($genre)
        ->create(['released_at' => now()->subYears(5)]);

    $date = now()->subMonth()->format('Y-m-d');

    $response = $this->get("/games?released_after=$date");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games.data', 1)
                        ->where('games.data.0.slug', $game->slug)
                )
        );
});

test('games.index displays only games that was released before date', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR->value]);
    $genre = Genre::factory()->create();
    Game::factory()
        ->for($user, 'creator')
        ->for($genre)
        ->create(['released_at' => now()]);
    $game = Game::factory()
        ->for($user, 'creator')
        ->for($genre)
        ->create(['released_at' => now()->subYears(5)]);

    $date = now()->subMonth()->format('Y-m-d');

    $response = $this->get("/games?released_before=$date");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games.data', 1)
                        ->where('games.data.0.slug', $game->slug)
                )
        );
});

test('games.index returns no games for an unknown genre', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR->value]);
    $genre = Genre::factory()->create(['name' => 'First']);
    Game::factory()
        ->for($user, 'creator')
        ->for($genre)
        ->create();

    $response = $this->get("/games?genre=Nope");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('games.data', 0)
                )
        );
});

test('games.index rejects an invalid date', function () {
    $response = $this->get('/games?released_after=abc');
    
    $response->assertSessionHasErrors('released_after');
});

test('games.index rejects a date range that ends before it starts', function () {
    $response = $this->get('/games?released_after=2026-06-01&released_before=2026-01-01');
    
    $response->assertSessionHasErrors('released_before');
});
