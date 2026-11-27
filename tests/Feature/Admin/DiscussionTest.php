<?php

use App\Enum\UserRole;
use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    Discussion::factory(5)->create();
});

test('admin.discussions.index returns inertia response with list of discussions', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.discussions.index"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/Discussion')
                ->has('discussions.data', 5)
        );
});

test('admin.discussions.index will redirect when wrong column or order is provided', function (string $key, string $value) {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.discussions.index", [$key => $value]))
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'wrong column' => ['column', 'wrong'],
    'wrong order' => ['order', 'csa']
]);
