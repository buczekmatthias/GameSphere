<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('user.profile passes for anyone', function () {
    $this
        ->get(route("user.profile", ['user' => User::factory()->create()]))
        ->assertStatus(200);

    $this
        ->actingAs(User::factory()->create())
        ->get(route("user.profile", ['user' => User::factory()->create()]))
        ->assertStatus(200);
});

test('user.profile fails with not found when trying to open tab not specified in UserProfileTab enum', function () {
    $this
        ->get(route("user.profile", ['user' => User::factory()->create(), 'tab' => 'wrong-value']))
        ->assertNotFound();
});

test('user.profile returns inertia response with user data, list of tabs and active tab', function () {
    $this
        ->get(route("user.profile", ['user' => User::factory()->create()]))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/UserProfile')
                ->has('user')
                ->has('tabs')
                ->has('activeTab')
        );
});

test('user.profile will return 404 for missing entry', function () {
    $this
        ->get(route("user.profile", ['user' => 'not-existing']))
        ->assertNotFound();
});
