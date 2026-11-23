<?php

use App\Enum\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    User::factory(5)->create();
});

test('admin.users.index returns inertia response with list of users', function () {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get("/admin/users");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/User')
                ->has('users.data', 6)
                ->has('roles', count(UserRole::cases()))
                ->has('roles_user_can_manage')
                ->has('game_creator_requests_count')
        );
});

test('admin.users.index will redirect when wrong column or order is provided', function (string $key, string $value) {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get("/admin/users?$key=$value");

    $response
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'wrong column' => ['column', 'wrong'],
    'wrong order' => ['order', 'csa']
]);
