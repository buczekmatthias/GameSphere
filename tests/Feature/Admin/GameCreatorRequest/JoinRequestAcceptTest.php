<?php

use App\Enum\UserRole;
use App\Models\GameCreatorRequest;
use App\Models\User;

test('admin.game.creator.join.accept will update role of user to game creator and redirect', function () {
    $request = GameCreatorRequest::factory()->create();

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch("/admin/game-creator-join-requests/{$request->user->username}/accept");

    $response->assertRedirect();

    $this->assertDatabaseHas('users', [
        'username' => $request->user->username,
        'role' => UserRole::GAME_CREATOR->value
    ]);
});
