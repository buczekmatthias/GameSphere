<?php

use App\Models\Review;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Str;

test('anyone can visit reviews.show', function () {
    $review = Review::factory()->create();

    $this
        ->get(route('reviews.show', ['review' => $review]))
        ->assertStatus(200);

    $this
        ->actingAs(User::factory()->create())
        ->get(route('reviews.show', ['review' => $review]))
        ->assertStatus(200);
});

test('reviews.show returns inertia response with review content', function () {
    $review = Review::factory()->create();

    $this
        ->get(route('reviews.show', ['review' => $review]))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/review/Show')
                ->has('review')
                ->where('review.slug', $review->slug)
                ->where('review.content', $review->content)
        );
});

test('reviews.show will return 404 if entry is missing', function () {
    $this
        ->get(route('reviews.show', ['review' => Str::uuid()]))
        ->assertNotFound();
});
