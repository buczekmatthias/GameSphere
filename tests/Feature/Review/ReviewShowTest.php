<?php

use App\Enum\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('anyone can visit reviews.show', function () {
    $user = User::factory()->create(['role' => UserRole::GAME_CREATOR]);
    $review = getReview($user);

    $guestResponse = $this->get("/reviews/{$review->slug}");
    $authenticatedResponse = $this->actingAs(User::factory()->create())->get("/reviews/{$review->slug}");

    $guestResponse->assertStatus(200);
    $authenticatedResponse->assertStatus(200);
});

test('reviews.show returns inertia response with review content', function () {
    $review = getReview();

    $response = $this->get("/reviews/{$review->slug}");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/review/Show')
                ->has('review')
                ->where('review.slug', $review->slug)
                ->where('review.content', $review->content)
        );
});
