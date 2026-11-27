<?php

use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('anyone can visit games.index', function () {
    $this
        ->get(route("games.index"))
        ->assertStatus(200);

    $this
        ->actingAs(User::factory()->create())
        ->get(route("games.index"))
        ->assertStatus(200);
});

test('games.index displays games and provides genres', function () {
    $genres = Genre::factory(3)->create();
    Game::factory(5)->for($genres->first())->create();

    $this
        ->get(route("games.index"))
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
    Game::factory()->create(['title' => 'First title']);
    Game::factory()->create(['title' => 'Second title']);

    $this
        ->get(route("games.index", ['title' => 'first']))
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
    Game::factory()
        ->for(Genre::factory()->create(['name' => 'First']))
        ->create();
    Game::factory()
        ->for(Genre::factory()->create(['name' => 'Second']))
        ->create();

    $this
        ->get(route("games.index", ['genre' => 'first']))
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
    Game::factory()->create(['released_at' => now()]);
    Game::factory()->create(['released_at' => now()->subYears(5)]);

    $this
        ->get(route("games.index", [
                'released_after' => now()->subMonth()->format('Y-m-d'),
                'released_before' => now()->addMonth()->format('Y-m-d')
        ]))
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

test('games.index displays only games that was released after date', function () {
    Game::factory()->create(['released_at' => now()]);
    Game::factory()->create(['released_at' => now()->subYears(5)]);

    $this
        ->get(route("games.index", ['released_after' => now()->subMonth()->format('Y-m-d')]))
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

test('games.index displays only games that was released before date', function () {
    Game::factory()->create(['released_at' => now()]);
    Game::factory()->create(['released_at' => now()->subYears(5)]);

    $this
        ->get(route("games.index", ['released_before' => now()->subMonth()->format('Y-m-d')]))
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

test('games.index returns no games for an unknown genre', function () {
    Game::factory()
        ->for(Genre::factory()->create(['name' => 'First']))
        ->create();

    $this
        ->get(route("games.index", ['genre' => 'Nope']))
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
    $this
        ->get(route("games.index", ["released_after" => "abc"]))
        ->assertSessionHasErrors('released_after');
});

test('games.index rejects a date range that ends before it starts', function () {
    $this
        ->get(route("games.index", [
            "released_after" => "2027-01-01",
            "released_before" => "2026-01-01"
        ]))
        ->assertSessionHasErrors('released_before');
});
