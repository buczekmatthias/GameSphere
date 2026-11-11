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

test('users.index returns only users matching criteria', function (array $filters, array $expected) {
    User::factory()->create(['name' => 'John Doe', 'username' => 'john.doe3', 'role' => UserRole::ADMIN]);
    User::factory()->create(['name' => 'Jane Marx', 'username' => 'jane.marx2', 'role' => UserRole::MODERATOR]);

    $response = $this->get("/users?".http_build_query($filters));

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/Users')
                ->has('roles', count(array_column(UserRole::cases(), 'value')))
                ->loadDeferredProps(
                    fn (AssertableInertia $reload) => $reload
                        ->where('users.data', fn ($users) => collect($users)->pluck('username')->sort()->values()->all() === $expected)
                )
        );
})->with([
    'name - doe' => [['contains' => 'doe'], ['john.doe3']],
    'username - 2' => [['contains' => '2'], ['jane.marx2']],
    'role - admin' => [['role' => UserRole::ADMIN->value], ['john.doe3']],
    'name - doe & role - admin' => [['contains' => 'doe', 'role' => UserRole::ADMIN->value], ['john.doe3']],
    'name - doe & role - moderator' => [['contains' => 'doe', 'role' => UserRole::MODERATOR->value], []],
    'no filters' => [[], ['jane.marx2', 'john.doe3']],
]);
