<?php

use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    Genre::factory(5)->create();
});

test('anyone can visit genres.index', function () {
    $this
        ->get(route("genres.index"))
        ->assertStatus(200);

    $this
        ->actingAs(User::factory()->create())
        ->get(route("genres.index"))
        ->assertStatus(200);
});

test('genres.index returns inertia response with deferred list of genres', function () {
    $this
        ->get(route("genres.index"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/genre/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has("genres.data", 5)
                        ->missing('genres.0.is_user_favorite')
                )
        );
});

test('genres returned by genres.index contain is_user_favorite field if user is authenticated', function () {
    $this
        ->actingAs(User::factory()->create())
        ->get(route("genres.index"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/genre/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has("genres.data", 5)
                        ->has('genres.data.0.is_user_favorite')
                )
        );
});
