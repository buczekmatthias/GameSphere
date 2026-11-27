<?php

use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    Discussion::factory(5)
        ->for(Genre::factory()->create(), 'discussable')
        ->create();
});

test('anyone can visit discussions.index', function () {
    $this
        ->get(route("discussions.index"))
        ->assertStatus(200);

    $this
        ->actingAs(User::factory()->create())
        ->get(route("discussions.index"))
        ->assertStatus(200);
});

test('discussions.index returns inertia response with list of discussions', function () {
    $this
        ->get(route("discussions.index"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/discussion/Index')
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('discussions.data', 5)
                )
        );
});
