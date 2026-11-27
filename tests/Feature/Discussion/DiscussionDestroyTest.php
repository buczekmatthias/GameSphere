<?php

use App\Enum\UserRole;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

test('discussions.destroy will redirect guest to login page', function () {
    $this
        ->delete(route("discussions.destroy", ['discussion' => Discussion::factory()->create()]))
        ->assertRedirect(route("security.login"));
});

test('discussions.destroy will pass only for owner or staff', function (UserRole|string $role, int $expectedStatus) {
    $discussion = Discussion::factory()->create();
    $user = match($role) {
        'owner' => $discussion->author,
        default => User::factory()->create(['role' => $role])
    };

    $this
        ->actingAs($user)
        ->delete(route("discussions.destroy", ['discussion' => $discussion]))
        ->assertStatus($expectedStatus);
})->with([
    'owner' => ['owner', 302],
    'user' => [UserRole::USER, 403],
    'creator' => [UserRole::GAME_CREATOR, 403],
    'mod' => [UserRole::MODERATOR, 302],
    'admin' => [UserRole::ADMIN, 302],
    'dev' => [UserRole::DEVELOPER, 302],
]);

test('discussions.destroy will remove discussion and related files', function () {
    Storage::fake('public');

    $slug = Str::orderedUuid();
    $commentSlug = Str::orderedUuid();
    $file = putFakeFile("discussions/{$slug}/{$commentSlug}");

    $discussion = Discussion::factory()->create();
    Comment::factory()->for($discussion)->create([
        'slug' => $commentSlug,
        'content' => 'Example comment content',
        'media' => [$file]
    ]);

    Storage::disk('public')->assertExists("discussions/{$slug}/{$commentSlug}/{$file}");

    $this
       ->actingAs($discussion->author)
       ->delete(route("discussions.destroy", ['discussion' => $discussion]))
       ->assertRedirect();

    $this->assertDatabaseEmpty('discussions');

    Storage::disk('public')->assertMissing("discussions/{$discussion->slug}");
});

test('discussions.destroy returns 404 for missing entry', function () {
    $this
       ->actingAs(User::factory()->create())
       ->delete(route("discussions.destroy", ['discussion' => Str::uuid()]))
       ->assertNotFound();
});
