<?php

use App\Enum\UserRole;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('comments.destory redirects to login page for guest', function () {
    $this
        ->delete(route("comments.destroy", ['comment' => Comment::factory()->create()]))
        ->assertRedirect(route('security.login'));
});

test('comments.destory deletes comment only for owner or staff', function (UserRole|string $role, int $expectedStatus, int $expectedCount) {
    Storage::fake('public');

    $discussion = Discussion::factory()->create();
    $slug = Str::orderedUuid();
    $path = "discussions/{$discussion->slug}/{$slug}";
    $fileName = putFakeFile($path);
    $comment = Comment::factory()->for($discussion)->create(['slug' => $slug, 'media' => [$fileName]]);

    $user = match($role) {
        'owner' => $comment->user,
        default => User::factory()->create(['role' => $role]),
    };

    Storage::disk('public')->assertExists("{$path}/{$fileName}");

    $this
        ->actingAs($user)
        ->delete(route("comments.destroy", ['comment' => $comment]))
        ->assertStatus($expectedStatus)
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('comments', $expectedCount);

    match($expectedCount) {
        0 => Storage::disk('public')->assertMissing($path),
        1 => Storage::disk('public')->assertExists("{$path}/{$fileName}")
    };
})->with([
    'owner' => ['owner', 302, 0],
    'user' => [UserRole::USER, 403, 1],
    'creator' => [UserRole::GAME_CREATOR, 403, 1],
    'mod' => [UserRole::MODERATOR, 302, 0],
    'admin' => [UserRole::ADMIN, 302, 0],
    'dev' => [UserRole::DEVELOPER, 302, 0],
]);

test('comments.destroy returns 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create())
        ->delete(route("comments.destroy", ['comment' => Str::uuid()]))
        ->assertNotFound();
});
