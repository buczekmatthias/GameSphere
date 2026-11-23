<?php

use App\Enum\UserRole;
use App\Models\User;

test('admin.users.destroy will pass only when current user is role above managed user', function (UserRole $role, int $expectedStatus) {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $response = $this
        ->actingAs(User::factory()->create(['role' => $role]))
        ->delete("/admin/users/{$otherUser->username}");

    $response->assertStatus($expectedStatus);
})->with([
    'mod against mod' => [UserRole::MODERATOR, 403],
    'admin against mod' => [UserRole::ADMIN, 302]
]);

test('admin.users.destroy will properly delete user', function () {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->delete("/admin/users/{$otherUser->username}");

    $response->assertRedirect();

    $this->assertDatabaseMissing('users', [
        'username' => $otherUser->username,
    ]);
});
