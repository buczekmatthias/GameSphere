<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('user.profile passes for anyone', function () {
    $u = User::factory()->create();
    $guestResponse = $this->get("/u/{$u->username}");
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/u/{$u->username}");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('user.profile fails with not found when trying to open tab not specified in UserProfileTab enum', function () {
    $u = User::factory()->create();

    $response = $this->get("/u/{$u->username}/wrong-value");

    $response->assertNotFound();
});

test('user.profile returns inertia response with user data, list of tabs and active tab', function () {
    $u = User::factory()->create();

    $response = $this->get("/u/{$u->username}");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/UserProfile')
                ->has('user')
                ->has('tabs')
                ->has('activeTab')
        );
});
