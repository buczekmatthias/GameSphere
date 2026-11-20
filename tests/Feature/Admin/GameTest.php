<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);

    Game::factory(5)
        ->for($user, 'creator')
        ->create();
});

test('admin.games.index returns inertia response with list of games', function () {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get("/admin/games");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/Game')
                ->has('games.data', 5)
        );
});

test('admin.games.index will redirect when wrong column or order is provided', function (string $key, string $value) {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get("/admin/games?$key=$value");

    $response
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'wrong column' => ['column', 'wrong'],
    'wrong order' => ['order', 'csa']
]);
