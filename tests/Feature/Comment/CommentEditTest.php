<?php

use App\Enum\UserRole;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

function getComment(?User $user = null): Comment
{
    $user = $user ?: User::factory()->create();

    return Comment::factory()
        ->for(
            Discussion::factory()
                ->for(Genre::factory()->create(), 'discussable')
                ->for($user, 'author')
                ->create()
        )
        ->for($user)
        ->create();

};


test('comments.edit redirects to login page for guest', function () {
    $comment = getComment();
    $response = $this->get("/comments/{$comment->slug}/edit");

    $response->assertRedirect(route('security.login'));
});

test('comments.edit passes only for owner or staff', function (string $actor, int $expectedStatus) {
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

    $response = $this->actingAs($user)->get("/comments/{$comment->slug}/edit");

    $response->assertStatus($expectedStatus);
})->with([
    'owner can' => ['owner', 200],
    'moderator can' => ['moderator', 200],
    'admin can' => ['admin', 200],
    'dev can' => ['dev', 200],
    'non-owner creator cannot' => ['creator', 403],
    'regular user cannot' => ['user', 403],
]);

test('comments.edit provides inertia response with comment', function () {
    $owner = User::factory()->create();
    $comment = getComment($owner);

    $response = $this->actingAs($owner)->get("/comments/{$comment->slug}/edit");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/comment/Edit')
                ->has('comment')
                ->where('comment.slug', $comment->slug)
                ->where('comment.content', $comment->content)
        );
});
