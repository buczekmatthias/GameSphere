<?php

use App\Enum\UserRole;
use App\Models\User;

test('comments.destory redirects to login page for guest', function () {
    $comment = getComment();
    $response = $this->delete("/comments/{$comment->slug}");

    $response->assertRedirect(route('security.login'));
});

test('comments.destory deletes comment only for owner or staff', function (string $actor, int $expectedStatus) {
    $owner = User::factory()->create(['role' => UserRole::USER]);
    $comment = getComment($owner);

    $user = match($actor) {
        'owner' => $owner,
        'moderator' => User::factory()->create(['role' => UserRole::MODERATOR]),
        'admin' => User::factory()->create(['role' => UserRole::ADMIN]),
        'dev' => User::factory()->create(['role' => UserRole::DEVELOPER]),
        'creator' => User::factory()->create(['role' => UserRole::GAME_CREATOR]),
        'user' => User::factory()->create(['role' => UserRole::USER]),
    };

    $response = $this->actingAs($user)->delete("/comments/{$comment->slug}");

    $response->assertStatus($expectedStatus);

    $this->assertDatabaseCount('comments', $expectedStatus === 302 ? 0 : 1);
})->with([
    'owner can' => ['owner', 302],
    'moderator can' => ['moderator', 302],
    'admin can' => ['admin', 302],
    'dev can' => ['dev', 302],
    'non-owner creator cannot' => ['creator', 403],
    'regular user cannot' => ['user', 403],
]);
