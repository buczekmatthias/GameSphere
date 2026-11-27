<?php

use App\Enum\UserRole;
use App\Models\Comment;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Str;

test('comments.edit redirects to login page for guest', function () {
    $this
        ->get(route("comments.edit", ['comment' => Comment::factory()->create()]))
        ->assertRedirect(route('security.login'));
});

test('comments.edit passes only for owner or staff', function (UserRole|string $role, int $expectedStatus) {
    $comment = Comment::factory()->create();

    $user = match($role) {
        'owner' => $comment->user,
        default => User::factory()->create(['role' => $role]),
    };

    $this
        ->actingAs($user)
        ->get(route("comments.edit", ['comment' => $comment]))
        ->assertStatus($expectedStatus);
})->with([
    'owner' => ['owner', 200],
    'user' => [UserRole::USER, 403],
    'creator' => [UserRole::GAME_CREATOR, 403],
    'mod' => [UserRole::MODERATOR, 200],
    'admin' => [UserRole::ADMIN, 200],
    'dev' => [UserRole::DEVELOPER, 200],
]);

test('comments.edit provides inertia response with comment', function () {
    $comment = Comment::factory()->create();

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("comments.edit", ['comment' => $comment]))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/comment/Edit')
                ->has('comment')
                ->where('comment.slug', $comment->slug)
                ->where('comment.content', $comment->content)
        );
});

test('comments.edit returns 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create())
        ->get(route("comments.edit", ['comment' => Str::uuid()]))
        ->assertNotFound();
});
