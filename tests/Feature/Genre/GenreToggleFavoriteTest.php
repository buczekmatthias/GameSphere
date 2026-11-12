<?php

use App\Models\Genre;
use App\Models\User;

test('guest will be redirected to login page', function () {
    $genre = Genre::factory()->create();
    $response = $this->post("/genres/{$genre->slug}/toggle-favorite");

    $response->assertRedirect(route('security.login'));
});

test('genres.favorite will properlly toggle favorite of genre for authenticated user', function (bool $isAttached) {
    $user = User::factory()->create();
    $genre = Genre::factory()->create();

    if ($isAttached) {
        $user->genres()->attach($genre);
    }

    $response = $this->actingAs($user)->from("/genres/{$genre->slug}")->post("/genres/{$genre->slug}/toggle-favorite");

    $response->assertRedirect(route("genres.show", ['genre' => $genre->slug]));

    $this->assertDatabaseCount('genre_user', $isAttached ? 0 : 1);
})->with([
    'attached' => true,
    'not attached' => false
]);
