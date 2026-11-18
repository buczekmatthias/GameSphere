<?php

use App\Enum\UserRole;
use App\Models\User;

test('discussions.update will redirect guest to login page', function () {
    $discussion = getDiscussion();

    $response = $this->patch("/discussions/{$discussion->slug}");

    $response->assertRedirect(route("security.login"));
});

test('discussions.update will pass only for owner or staff', function (string $actor, int $expectedStatus) {
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
        ->patch("/discussions/{$discussion->slug}", [
            'title' => 'New title'
        ]);

    $response->assertStatus($expectedStatus);
})->with([
    'owner can' => ['owner', 302],
    'moderator can' => ['moderator', 302],
    'admin can' => ['admin', 302],
    'dev can' => ['dev', 302],
    'non-owner creator cannot' => ['creator', 403],
    'regular user cannot' => ['user', 403],
]);

test('discussions.update successfully updates discussion content with valid payload', function () {
    $discussion = getDiscussion();

    $response = $this
        ->actingAs($discussion->author)
        ->patch("/discussions/{$discussion->slug}", [
            'title' => 'New title'
        ]);

    $response->assertRedirect();

    $discussion->refresh();

    $this->assertDatabaseHas('discussions', [
        'slug' => $discussion->slug,
        'title' => 'New title'
    ]);
});

test('discussions.update will redirect with error for invalid payload', function () {
    $discussion = getDiscussion();

    $response = $this
        ->actingAs($discussion->author)
        ->patch("/discussions/{$discussion->slug}", [
            'title' => ''
        ]);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('title');

    $this->assertDatabaseMissing('discussions', [
        'slug' => $discussion->slug,
        'title' => ''
    ]);
});
