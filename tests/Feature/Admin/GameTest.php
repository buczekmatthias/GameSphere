<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    Game::factory(5)->create();
});

test('admin.games.index returns inertia response with list of games', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.games.index"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/Game')
                ->has('games.data', 5)
        );
});

test('admin.games.index will redirect when wrong column or order is provided', function (string $key, string $value) {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.games.index", [$key => $value]))
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'wrong column' => ['column', 'wrong'],
    'wrong order' => ['order', 'csa']
]);
