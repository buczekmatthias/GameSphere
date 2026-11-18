<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('anyone can visit discussions.show', function () {
    $discussion = getDiscussion();

    $guestResponse = $this->get("/discussions/{$discussion->slug}");
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/discussions/{$discussion->slug}");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('discussions.show returns inertia response with discussion content and comment count', function () {
    $discussion = getDiscussion();

    $response = $this->get("/discussions/{$discussion->slug}");

    $response
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
