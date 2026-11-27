<?php

use App\Enum\UserRole;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Support\Str;

test('discussions.update will redirect guest to login page', function () {
    $this
        ->patch(route("discussions.update", ['discussion' => Discussion::factory()->create()]))
        ->assertRedirect(route("security.login"));
});

test('discussions.update will pass only for owner or staff', function (UserRole|string $role, int $expectedStatus) {
    $discussion = Discussion::factory()->create();

    $user = match($role) {
        'owner' => $discussion->author,
        default => User::factory()->create(['role' => $role])
    };

    $this
        ->actingAs($user)
        ->patch(route("discussions.update", ['discussion' => $discussion]), [
            'title' => 'New title'
        ])
        ->assertStatus($expectedStatus);
})->with([
    'owner' => ['owner', 302],
    'user' => [UserRole::USER, 403],
    'creator' => [UserRole::GAME_CREATOR, 403],
    'mod' => [UserRole::MODERATOR, 302],
    'admin' => [UserRole::ADMIN, 302],
    'dev' => [UserRole::DEVELOPER, 302],
]);

test('discussions.update successfully updates discussion content with valid payload', function () {
    $discussion = Discussion::factory()->create();

    $this
        ->actingAs($discussion->author)
        ->patch(route("discussions.update", ['discussion' => $discussion]), [
            'title' => 'New title'
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $discussion->refresh();

    $this->assertDatabaseHas('discussions', [
        'slug' => $discussion->slug,
        'title' => 'New title'
    ]);
});

test('discussions.update will redirect with error for invalid payload', function () {
    $discussion = Discussion::factory()->create();

    $this
        ->actingAs($discussion->author)
        ->patch(route("discussions.update", ['discussion' => $discussion]), [
            'title' => ''
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('title');

    $this->assertDatabaseMissing('discussions', [
        'slug' => $discussion->slug,
        'title' => ''
    ]);
});

test('discussions.update returns 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create())
        ->patch(route("discussions.update", ['discussion' => Str::uuid()]), [
            'title' => ''
        ])
        ->assertNotFound();
});
