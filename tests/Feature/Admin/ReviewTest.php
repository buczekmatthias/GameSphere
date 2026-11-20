<?php

use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Review;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);

    Review::factory(5)
        ->for(Game::factory()->for($user, 'creator')->create())
        ->for($user)
        ->create();

});

test('admin.reviews.index returns inertia response with list of reviews', function () {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get("/admin/reviews");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/Review')
                ->has('reviews.data', 5)
        );
});

test('admin.reviews.index will redirect when wrong column or order is provided', function (string $key, string $value) {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get("/admin/reviews?$key=$value");

    $response
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'wrong column' => ['column', 'wrong'],
    'wrong order' => ['order', 'csa']
]);
