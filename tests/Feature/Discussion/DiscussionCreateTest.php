<?php

use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('discussions.create will redirect guest to login page', function () {
    $this
        ->get(route("discussions.create", ['type' => 'genre', 'slug' => Genre::factory()->create()]))
        ->assertRedirect(route("security.login"));
});

test('discussions.create will return inertia response with discussable type and matching item for authenticated user', function () {
    $this
        ->actingAs(User::factory()->create())
        ->get(route("discussions.create", ['type' => 'genre', 'slug' => Genre::factory()->create()]))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/discussion/Create')
                ->has('type')
                ->has('item')
        );
});

test('unknown type in discussions.create will cause 404 (allowed game/genre)', function () {
    $this
        ->actingAs(User::factory()->create())
        ->get(route("discussions.create", ['type' => 'wrong-type', 'slug' => Genre::factory()->create()]))
        ->assertNotFound();
});
