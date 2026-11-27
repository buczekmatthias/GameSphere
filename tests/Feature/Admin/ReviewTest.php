<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Review;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    Review::factory(5)->create();
});

test('admin.reviews.index returns inertia response with list of reviews', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.reviews.index"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/Review')
                ->has('reviews.data', 5)
        );
});

test('admin.reviews.index will redirect when wrong column or order is provided', function (string $key, string $value) {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.reviews.index", [$key => $value]))
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'wrong column' => ['column', 'wrong'],
    'wrong order' => ['order', 'csa']
]);
