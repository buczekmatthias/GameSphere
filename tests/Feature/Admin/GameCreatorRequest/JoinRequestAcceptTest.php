<?php

use App\Enum\UserRole;
use App\Models\GameCreatorRequest;
use App\Models\User;

test('admin.game.creator.join.accept will update role of user to game creator and redirect', function () {
    $request = GameCreatorRequest::factory()->create();

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(route("admin.game.creator.join.accept", ['user' => $request->user]))
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'username' => $request->user->username,
        'role' => UserRole::GAME_CREATOR->value
    ]);
});

test('admin.game.creator.join.accept returns 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(route("admin.game.creator.join.accept", ['user' => 'inexistent-user']))
        ->assertNotFound();
});
