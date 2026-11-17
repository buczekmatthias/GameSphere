<?php

use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->genre = Genre::factory()->create();
});

test('discussions.create will redirect guest to login page', function () {
    $response = $this->get("/genre/{$this->genre->slug}/discussions/create");

    $response->assertRedirect(route("security.login"));
});

test('discussions.create will return inertia response with discussable type and matching item for authenticated user', function () {
    $response = $this
        ->actingAs(
            User::factory()->create()
        )->get("/genre/{$this->genre->slug}/discussions/create");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/discussion/Create')
                ->has('type')
                ->has('item')
        );
});

test('unknown type in discussions.create will cause 404 (allowed game/genre)', function () {
    $response = $this
        ->actingAs(
            User::factory()->create()
        )->get("/wrong-type/{$this->genre->slug}/discussions/create");

    $response->assertNotFound();
});
