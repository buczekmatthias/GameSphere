<?php

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('games.create redirects to login page for guest', function () {
    $this
        ->get(route('games.create'))
        ->assertRedirect(route('security.login'));
});

test('games.create fails for regular user', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::USER]))
        ->get(route('games.create'))
        ->assertForbidden();
});

test('any authorized user with role different than USER can access games.create', function (UserRole $role) {
    $this
        ->actingAs(User::factory()->create(['role' => $role]))
        ->get(route('games.create'))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/game/Create')
        );

})->with([
    'game creator' => UserRole::GAME_CREATOR,
    'moderator' => UserRole::MODERATOR,
    'admin' => UserRole::ADMIN,
    'developer' => UserRole::DEVELOPER,
]);

test('games.create provides list of genres', function () {
    Genre::factory(3)->create();

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::GAME_CREATOR]))
        ->get(route('games.create'))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->has('genres', 3)
        );
});
