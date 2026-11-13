<?php

use App\Enum\UserRole;
use App\Models\User;

test('reviews.destroy redirects for guest', function () {
    $review = getReview();

    $response = $this->delete("/reviews/{$review->slug}");

    $response->assertRedirect(route('security.login'));
});

test('reviews.destroy properly deletes review for owner and redirects', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $review = getReview($user);

    $this->assertDatabaseCount('reviews', 1);
    
    $response = $this->actingAs($user)->delete("/reviews/{$review->slug}");

    $response->assertRedirect();

    $this->assertDatabaseCount('reviews', 0);
});

test('reviews.destroy properly deletes review for staff and redirects', function (UserRole $role) {
    $review = getReview();

    $this->assertDatabaseCount('reviews', 1);
    
    $response = $this->actingAs(User::factory()->create(['role' => $role]))->delete("/reviews/{$review->slug}");

    $response->assertRedirect();

    $this->assertDatabaseCount('reviews', 0);
})->with([
    'moderator' => UserRole::MODERATOR,
    'admin' => UserRole::ADMIN,
    'developer' => UserRole::DEVELOPER,
]);

test('reviews.destroy fails for non-owner and non-staff', function () {
    $review = getReview();
    
    $response = $this->actingAs(User::factory()->create())->delete("/reviews/{$review->slug}");

    $response->assertForbidden();

    $this->assertDatabaseCount('reviews', 1);
});
