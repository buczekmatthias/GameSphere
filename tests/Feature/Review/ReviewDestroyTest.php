<?php

use App\Enum\UserRole;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Str;

test('reviews.destroy redirects for guest', function () {
    $this
        ->delete(route("reviews.destroy", ['review' => Review::factory()->create()]))
        ->assertRedirect(route('security.login'));
});

test('reviews.destroy passes only for owner and staff', function (UserRole|string $role, int $expectedStatus, int $expectedCount) {
    $review = Review::factory()->create();
    $user = match($role) {
        'owner' => $review->user,
        default => User::factory()->create(['role' => $role]),
    };

    $this->assertDatabaseCount('reviews', 1);

    $this
        ->actingAs($user)
        ->delete(route("reviews.destroy", ['review' => $review]))
        ->assertStatus($expectedStatus);

    $this->assertDatabaseCount('reviews', $expectedCount);
})->with([
    'owner' => ['owner', 302, 0],
    'user' => [UserRole::USER, 403, 1],
    'creator' => [UserRole::GAME_CREATOR, 403, 1],
    'mod' => [UserRole::MODERATOR, 302, 0],
    'admin' => [UserRole::ADMIN, 302, 0],
    'dev' => [UserRole::DEVELOPER, 302, 0],
]);

test('reviews.destroy throws 404 on missing entry', function () {
    $this
       ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
       ->delete(route("reviews.destroy", ['review' => Str::uuid()]))
       ->assertNotFound();
});
