<?php

use App\Models\Discussion;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Str;

test('anyone can visit discussions.show', function () {
    $discussion = Discussion::factory()->create();

    $this
        ->get(route("discussions.show", ['discussion' => $discussion]))
        ->assertStatus(200);

    $this
        ->actingAs(User::factory()->create())
        ->get(route("discussions.show", ['discussion' => $discussion]))
        ->assertStatus(200);
});

test('discussions.show returns inertia response with discussion content and comment count', function () {
    $discussion = Discussion::factory()->create();

    $this
        ->get(route("discussions.show", ['discussion' => $discussion]))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/discussion/Show')
                ->has('discussion')
                ->where('discussion.slug', $discussion->slug)
                ->where('discussion.title', $discussion->title)
                ->has('discussion.comments_count')
        );
});

test('discussions.show returns 404 for missing entries', function () {
    $this
        ->get(route("discussions.show", ['discussion' => Str::uuid()]))
        ->assertNotFound();
});
