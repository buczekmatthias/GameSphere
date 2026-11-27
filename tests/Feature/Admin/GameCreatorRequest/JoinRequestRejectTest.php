<?php

use App\Enum\UserRole;
use App\Models\GameCreatorRequest;
use App\Models\User;

test('admin.game.creator.join.reject will remove request and redirect', function () {
    $request = GameCreatorRequest::factory()->create();

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(route("admin.game.creator.join.reject", ['user' => $request->user]))
        ->assertRedirect();

    $this->assertDatabaseMissing('users', [
        'username' => $request->user->username,
        'role' => UserRole::GAME_CREATOR->value
    ]);

    $this->assertDatabaseCount('game_creator_requests', 0);
});

test('admin.game.creator.join.reject returns 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(route("admin.game.creator.join.reject", ['user' => 'inexistent-user']))
        ->assertNotFound();
});
