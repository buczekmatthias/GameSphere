<?php

use App\Models\Comment;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Str;

test('anyone can visit comments.show', function () {
    $comment = Comment::factory()->create();

    $this
        ->get(route("comments.show", ['comment' => $comment]))
        ->assertStatus(200);

    $this
        ->actingAs(User::factory()->create())
        ->get(route("comments.show", ['comment' => $comment]))
        ->assertStatus(200);
});

test('comments.show returns inertia response with comment content', function () {
    $comment = Comment::factory()->create();

    $this
        ->get(route("comments.show", ['comment' => $comment]))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/comment/Show')
                ->has('comment')
                ->where('comment.slug', $comment->slug)
                ->where('comment.content', $comment->content)
        );
});

test('comments.show returns 404 for missing entry', function () {
    $this
        ->get(route("comments.show", ['comment' => Str::uuid()]))
        ->assertNotFound();
});
