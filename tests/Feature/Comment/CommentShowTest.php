<?php

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->discussion = Discussion::factory()
        ->for(Genre::factory()->create(), 'discussable')
        ->for(User::factory()->create(), 'author')
        ->create();
});

test('anyone can visit comments.show', function () {
    $user = User::factory()
        ->create();
    $comment = Comment::factory()
        ->for($this->discussion)
        ->for($user)
        ->create();

    $guestResponse = $this->get("/comments/{$comment->slug}");
    $authenticatedResponse = $this->actingAs($user)->get("/comments/{$comment->slug}");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('comments.show returns inertia response with comment content', function () {
    $user = User::factory()
        ->create();
    $comment = Comment::factory()
        ->for($this->discussion)
        ->for($user)
        ->create();

    $response = $this->get("/comments/{$comment->slug}");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/comment/Show')
                ->has('comment')
                ->where('comment.slug', $comment->slug)
                ->where('comment.content', $comment->content)
        );
});
