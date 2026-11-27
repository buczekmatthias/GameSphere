<?php

use App\Enum\UserRole;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Support\Str;

test('discussions.lock.toggle will redirect guest to login page', function () {
    $this
        ->patch(route("discussions.lock.toggle", ['discussion' => Discussion::factory()->create()]))
        ->assertRedirect(route("security.login"));
});

test('discussions.lock.toggle will pass only for owner or staff', function (UserRole|string $role, int $expectedStatus) {
    $discussion = Discussion::factory()->create();

    $user = match($role) {
        'owner' => $discussion->author,
        default => User::factory()->create(['role' => $role])
    };

    $this
        ->actingAs($user)
        ->patch(route("discussions.lock.toggle", ['discussion' => $discussion]))
        ->assertStatus($expectedStatus);
})->with([
    'owner' => ['owner', 302],
    'user' => [UserRole::USER, 403],
    'creator' => [UserRole::GAME_CREATOR, 403],
    'mod' => [UserRole::MODERATOR, 302],
    'admin' => [UserRole::ADMIN, 302],
    'dev' => [UserRole::DEVELOPER, 302],
]);

test('discussions.lock.toggle changes lock status', function () {
    $discussion = Discussion::factory()->create(['is_locked' => false]);

    expect($discussion->is_locked)->toBe(false);

    $this
        ->from(route('discussions.show', ['discussion' => $discussion->slug]))
        ->actingAs($discussion->author)
        ->patch(route("discussions.lock.toggle", ['discussion' => $discussion]))
        ->assertRedirect(route('discussions.show', ['discussion' => $discussion->slug]));

    $discussion->refresh();

    expect($discussion->is_locked)->toBe(true);
});

test('discussions.lock.toggle will return 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create())
        ->patch(route("discussions.lock.toggle", ['discussion' => Str::uuid()]))
        ->assertNotFound();
});
