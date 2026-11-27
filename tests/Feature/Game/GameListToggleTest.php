<?php

use App\Enum\GameCollectionType;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Str;

test('games.lists.toggle redirects to login page for guest', function () {
    $this
        ->post(
            route("games.lists.toggle", ['game' => Game::factory()->create()]),
            ['list' => GameCollectionType::FAVORITE->value]
        )
        ->assertRedirect(route('security.login'));
});

test('games.lists.toggle adds game to specified list for authenticated user', function (GameCollectionType $list) {
    $user = User::factory()->create();
    $game = Game::factory()->create(['released_at' => now()->yesterday()]);

    $this
        ->actingAs($user)
        ->post(
            route("games.lists.toggle", ['game' => $game]),
            ['list' => $list->value]
        )
        ->assertRedirect();

    $this->assertDatabaseHas('game_user', [
        'user_id' => $user->id,
        'game_id' => $game->id,
        'list_type' => $list->value
    ]);
})->with([
    'wishlist' => GameCollectionType::WISHLIST,
    'owned' => GameCollectionType::OWNED,
    'playing' => GameCollectionType::PLAYING,
    'completed' => GameCollectionType::COMPLETED,
    'favorite' => GameCollectionType::FAVORITE
]);

test('games.lists.toggle removes game from specified list for authenticated user', function (GameCollectionType $list) {
    $user = User::factory()->create();
    $game = Game::factory()->create(['released_at' => now()->yesterday()]);

    $user->games()->attach($game, ['list_type' => $list->value]);

    $this->assertDatabaseHas('game_user', [
        'user_id' => $user->id,
        'game_id' => $game->id,
        'list_type' => $list->value
    ]);

    $this
        ->actingAs($user)
        ->post(
            route("games.lists.toggle", ['game' => $game]),
            ['list' => $list->value]
        )
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('game_user', [
        'user_id' => $user->id,
        'game_id' => $game->id,
        'list_type' => $list->value
    ]);
})->with([
    'wishlist' => GameCollectionType::WISHLIST,
    'owned' => GameCollectionType::OWNED,
    'playing' => GameCollectionType::PLAYING,
    'completed' => GameCollectionType::COMPLETED,
    'favorite' => GameCollectionType::FAVORITE
]);

test('games.list.toggle will return 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create())
        ->post(
            route("games.lists.toggle", ['game' => Str::uuid()]),
            ['list' => GameCollectionType::WISHLIST->value]
        )
        ->assertNotFound();
});
