<?php

use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $genre = Genre::factory()->create();
    
    Discussion::factory(5)
        ->create([
            'discussable_id' => $genre->id,
            'discussable_type' => "App\\Models\\Genre"
        ]);
});

test('anyone can visit discussions.index', function () {
    $guestResponse = $this->get("/discussions");
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/discussions");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('discussions.index returns inertia response with list of discussions', function () {
    $response = $this->get("/discussions");

    $response
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
