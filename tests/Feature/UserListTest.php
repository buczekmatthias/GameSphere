<?php

use App\Enum\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('anyone can visit users.index', function () {
    $guestResponse = $this->get('/users');
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/users");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('users.index returns inertia component, list of users and list of roles', function () {
    User::factory(2)->create();
    
    $response = $this->get("/users");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/Users')
                ->has('roles', count(array_column(UserRole::cases(), 'value')))
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->has('users.data', 2)
                )
        );
});
