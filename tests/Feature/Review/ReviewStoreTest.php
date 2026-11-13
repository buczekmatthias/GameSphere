<?php

use App\Enum\ReviewKey;
use App\Enum\UserRole;
use App\Models\Game;
use App\Models\User;

beforeEach(function () {
    $this->game = Game::factory()
    ->for(User::factory()->create(['role' => UserRole::GAME_CREATOR]), 'creator')
    ->create();
});

function validReviewPayload(Game $game, array $override = []): array
{
    return array_merge(
        [
        'content' => 'valid content',
        'game_slug' => $game->slug,
        'ratings' => collect(array_column(ReviewKey::cases(), 'value'))
            ->mapWithKeys(function (string $key) {
                return [$key => fake()->numberBetween(1, 5)];
            })
            ->toArray()
        ],
        $override
    );
}

test('reviews.store redirects for guest', function () {
    $response = $this->post("/reviews", validReviewPayload($this->game));

    $response->assertRedirect(route('security.login'));
});

test('reviews.store creates new review with valid payload', function () {
    $user = User::factory()->create();
    $payload = validReviewPayload($this->game);

    $response = $this->actingAs($user)->post("/reviews", $payload);

    $response->assertRedirect();

    $this->assertDatabaseHas('reviews', [
        'game_id' => $this->game->id,
        'content' => $payload['content'],
        'user_id' => $user->id
    ]);
});

test('reviews.store fails with invalid payload', function (string $key, mixed $value) {
    $user = User::factory()->create();
    $payload = validReviewPayload($this->game, [$key => $value]);

    $response = $this->actingAs($user)->post("/reviews", $payload);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors();

    $this->assertDatabaseCount('reviews', 0);
})->with([
    'empty content' => ['content', ''],
    'invalid game slug' => ['game_slug', 'not-a-uuid'],
    'ratings missing keys' => ['ratings', ['gameplay' => 3]],
    'ratings has extra keys' => ['ratings', ['wrong_key' => 5]],
    'rating too high' => [
        'ratings',
        collect(array_column(ReviewKey::cases(), 'value'))
            ->mapWithKeys(function (string $key) {
                return [$key => fake()->numberBetween(6, 9)];
            })
            ->toArray()
    ],
    'rating too low' => [
        'ratings',
        collect(array_column(ReviewKey::cases(), 'value'))
            ->mapWithKeys(function (string $key) {
                return [$key => fake()->numberBetween(-5, -1)];
            })
            ->toArray()
    ]
]);
