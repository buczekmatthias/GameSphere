<?php

use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('anyone can visit genres.show', function () {
    $genre = Genre::factory()->create();

    $guestResponse = $this->get("/genres/{$genre->slug}");
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/genres/{$genre->slug}");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('genres.show returns inertia response with genre content', function () {
    $genre = Genre::factory()->create();

    $response = $this->get("/genres/{$genre->slug}");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/genre/Show')
                ->has('genre')
                ->where('genre.slug', $genre->slug)
                ->where('genre.name', $genre->name)
        );
});

test('genre returned by genres.show contain is_user_favorite field if user is authenticated', function () {
    $genre = Genre::factory()->create();

    $response = $this->actingAs(User::factory()->create())->get("/genres/{$genre->slug}");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/genre/Show')
                ->has("genre")
                ->has('genre.is_user_favorite')
                ->where('genre.slug', $genre->slug)
                ->where('genre.name', $genre->name)
        );
});
