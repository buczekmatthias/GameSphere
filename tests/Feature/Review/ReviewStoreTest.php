<?php

use App\Enum\ReviewKey;
use App\Models\Game;
use App\Models\User;

function getRatingArray(int $min = 1, int $max = 5): array
{
    return collect(array_column(ReviewKey::cases(), 'value'))
        ->mapWithKeys(fn (string $key) => [$key => fake()->numberBetween($min, $max)])
        ->toArray();
}

function validReviewPayload(Game $game, array $override = []): array
{
    return array_merge(
        [
        'content' => 'valid content',
        'game_slug' => $game->slug,
        'ratings' => getRatingArray()
        ],
        $override
    );
}

test('reviews.store redirects for guest', function () {
    $this
        ->post(route("reviews.store"), validReviewPayload(Game::factory()->create()))
        ->assertRedirect(route('security.login'));
});

test('reviews.store creates new review with valid payload', function () {
    $user = User::factory()->create();
    $game = Game::factory()->create();
    $payload = validReviewPayload($game);

    $this
        ->actingAs($user)
        ->post(route("reviews.store"), $payload)
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('reviews', [
        'game_id' => $game->id,
        'content' => $payload['content'],
        'user_id' => $user->id
    ]);
});

test('reviews.store fails with invalid payload', function (string $key, mixed $value) {
    $user = User::factory()->create();
    $payload = validReviewPayload(Game::factory()->create(), [$key => $value]);

    $this
        ->actingAs($user)
        ->post(route("reviews.store"), $payload)
        ->assertRedirect()
        ->assertSessionHasErrors();

    $this->assertDatabaseCount('reviews', 0);
})->with([
    'empty content' => ['content', ''],
    'invalid game slug' => ['game_slug', 'not-a-uuid'],
    'ratings missing keys' => ['ratings', ['gameplay' => 3]],
    'ratings has extra keys' => ['ratings', ['wrong_key' => 5]],
    'rating too high' => ['ratings', getRatingArray(7, 10)],
    'rating too low' => ['ratings', getRatingArray(-5, -1)]
]);
