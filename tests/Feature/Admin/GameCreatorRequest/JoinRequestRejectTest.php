<?php

use App\Enum\UserRole;
use App\Models\GameCreatorRequest;
use App\Models\User;

test('admin.game.creator.join.reject will remove request and redirect', function () {
    $request = GameCreatorRequest::factory()->create();

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch("/admin/game-creator-join-requests/{$request->user->username}/reject");

    $response->assertRedirect();

    $this->assertDatabaseMissing('users', [
        'username' => $request->user->username,
        'role' => UserRole::GAME_CREATOR->value
    ]);

    $this->assertDatabaseCount('game_creator_requests', 0);
});
