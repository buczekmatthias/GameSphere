<?php

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('games.create redirects to login page for guest', function () {
    $response = $this->get('/games/create');

    $response->assertRedirect(route('security.login'));
});

test('games.create fails for regular user', function () {
    $response = $this->actingAs(User::factory()->create(['role' => UserRole::USER]))->get('/games/create');

    $response->assertForbidden();
});

test('any authorized user with role different than USER can access games.create', function (UserRole $role) {
    $response = $this->actingAs(User::factory()->create(['role' => $role]))->get('/games/create');
        
    $response
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => $page->component('app/game/Create'));

})->with([
    'game creator' => UserRole::GAME_CREATOR,
    'moderator' => UserRole::MODERATOR,
    'admin' => UserRole::ADMIN,
    'developer' => UserRole::DEVELOPER,
]);

test('games.create provides genres', function () {
    Genre::factory(3)->create();

    $response = $this->actingAs(User::factory()->create(['role' => UserRole::GAME_CREATOR]))->get('/games/create');

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->has('genres', 3)
        );
});
