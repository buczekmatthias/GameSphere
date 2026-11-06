<?php

use App\Enum\GameCollectionType;
use App\Enum\UserRole;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;

test('games.lists.toggle redirects to login page for guest', function () {
    $game = Game::factory()->for(Genre::factory()->create())->for(User::factory()->create(['role' => UserRole::GAME_CREATOR]), 'creator')->create();

    $response = $this->post("games/{$game->slug}/toggle-list", [
        'list' => GameCollectionType::FAVORITE->value
    ]);

    $response->assertRedirect(route('security.login'));
});

test('games.lists.toggle adds game to specified list for authenticated user', function (GameCollectionType $list) {
    $user = User::factory()->create();
    $game = Game::factory()->for(Genre::factory()->create())->for(User::factory()->create(['role' => UserRole::GAME_CREATOR]), 'creator')->create(['released_at' => now()->yesterday()]);

    $response = $this->actingAs($user)->post("games/{$game->slug}/toggle-list", [
        'list' => $list->value
    ]);
    
    $response->assertRedirect();

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
    $game = Game::factory()->for(Genre::factory()->create())->for(User::factory()->create(['role' => UserRole::GAME_CREATOR]), 'creator')->create(['released_at' => now()->yesterday()]);

    $user->games()->attach($game, ['list_type' => $list->value]);

    $this->assertDatabaseHas('game_user', [
        'user_id' => $user->id,
        'game_id' => $game->id,
        'list_type' => $list->value
    ]);

    $response = $this->actingAs($user)->post("games/{$game->slug}/toggle-list", [
        'list' => $list->value
    ]);
    
    $response->assertRedirect();

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
