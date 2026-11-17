<?php

use App\Enum\UserRole;
use App\Models\User;

test('discussions.lock.toggle will redirect guest to login page', function () {
    $discussion = getDiscussion();

    $response = $this->patch("/discussions/{$discussion->slug}/toggle-lock");

    $response->assertRedirect(route("security.login"));
});

test('discussions.lock.toggle will pass only for owner or staff', function (string $actor, int $expectedStatus) {
    $discussion = getDiscussion();

    $user = match($actor) {
        'owner' => $discussion->author,
        'moderator' => User::factory()->create(['role' => UserRole::MODERATOR]),
        'admin' => User::factory()->create(['role' => UserRole::ADMIN]),
        'dev' => User::factory()->create(['role' => UserRole::DEVELOPER]),
        'creator' => User::factory()->create(['role' => UserRole::GAME_CREATOR]),
        'user' => User::factory()->create(['role' => UserRole::USER]),
    };

    $response = $this
        ->actingAs($user)
        ->patch("/discussions/{$discussion->slug}/toggle-lock");

    $response->assertStatus($expectedStatus);
})->with([
    'owner can' => ['owner', 302],
    'moderator can' => ['moderator', 302],
    'admin can' => ['admin', 302],
    'dev can' => ['dev', 302],
    'non-owner creator cannot' => ['creator', 403],
    'regular user cannot' => ['user', 403],
]);

test('discussions.lock.toggle changes lock status', function () {
    $discussion = getDiscussion();

    expect($discussion->is_locked)->toBe(false);

    $response = $this
        ->from(route('discussions.show', ['discussion' => $discussion->slug]))
        ->actingAs($discussion->author)
        ->patch("/discussions/{$discussion->slug}/toggle-lock");

    $response->assertRedirect(route('discussions.show', ['discussion' => $discussion->slug]));

    $discussion->refresh();

    expect($discussion->is_locked)->toBe(true);
});
