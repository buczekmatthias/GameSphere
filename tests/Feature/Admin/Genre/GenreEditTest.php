<?php

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Str;

test('admin.genres.edit returns inertia response with genre content', function () {
    $genre = Genre::factory()->create();

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.genres.edit", ['genre' => $genre]))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/genre/Edit')
                ->has('genre')
                ->where('genre.slug', $genre->slug)
                ->where('genre.name', $genre->name)
        );
});

test('admin.genres.edit will return 404 for missing genre', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.genres.edit", ['genre' => Str::uuid()]))
        ->assertNotFound();
});
