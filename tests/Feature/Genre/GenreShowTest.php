<?php

use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Str;

test('anyone can visit genres.show', function () {
    $genre = Genre::factory()->create();

    $this
        ->get(route('genres.show', ['genre' => $genre]))
        ->assertStatus(200);

    $this
        ->actingAs(User::factory()->create())
        ->get(route('genres.show', ['genre' => $genre]))
        ->assertStatus(200);
});

test('genres.show returns inertia response with genre content', function () {
    $genre = Genre::factory()->create();
    $this
        ->get(route('genres.show', ['genre' => $genre]))
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

    $this
        ->actingAs(User::factory()->create())
        ->get(route('genres.show', ['genre' => $genre]))
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

test('genres.show will return 404 if entry is missing', function () {
    $this
        ->get(route('genres.show', ['genre' => Str::uuid()]))
        ->assertNotFound();
});
