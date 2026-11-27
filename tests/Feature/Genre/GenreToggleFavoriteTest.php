<?php

use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Str;

test('guest will be redirected to login page', function () {
    $genre = Genre::factory()->create();
    
    $this
        ->post(route("genres.favorite", ['genre' => $genre]))
        ->assertRedirect(route('security.login'));
});

test('genres.favorite will properlly toggle favorite of genre for authenticated user', function (bool $isAttached, int $expectedCount) {
    $user = User::factory()->create();
    $genre = Genre::factory()->create();

    if ($isAttached) {
        $user->genres()->attach($genre);
    }

    $this
        ->actingAs($user)
        ->from(route("genres.show", ['genre' => $genre]))
        ->post(route("genres.favorite", ['genre' => $genre]))
        ->assertRedirect(route("genres.show", ['genre' => $genre]))
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('genre_user', $expectedCount);
})->with([
    'attached' => [true, 0],
    'not attached' => [false, 1]
]);

test('genres.favorite will return 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create())
        ->post(route("genres.favorite", ['genre' => Str::uuid()]))
        ->assertNotFound();
});
