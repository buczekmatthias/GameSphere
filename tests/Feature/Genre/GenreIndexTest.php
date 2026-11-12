<?php

use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('anyone can visit genres.index', function () {
    $guestResponse = $this->get("/genres");
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/genres");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('genres.index returns inertia response with deferred list of genres', function () {
    Genre::factory(5)->create();

    $response = $this->get("/genres");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/genre/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has("genres.data", 5)
                )
        );
});

test('genres returned by genres.index contain is_user_favorite field if user is authenticated', function () {
    Genre::factory(5)->create();

    $response = $this->actingAs(User::factory()->create())->get("/genres");

    $response
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
