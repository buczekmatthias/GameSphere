<?php

use App\Enum\UserRole;
use App\Models\User;

test('admin.users.role will pass only when current user is role above managed user', function (UserRole $role, int $expectedStatus) {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $response = $this
        ->actingAs(User::factory()->create(['role' => $role]))
        ->patch("/admin/users/{$otherUser->username}/role", [
            'role' => UserRole::ADMIN->value
        ]);

    $response->assertStatus($expectedStatus);
})->with([
    'mod against mod' => [UserRole::MODERATOR, 403],
    'admin against mod' => [UserRole::ADMIN, 302]
]);

test('admin.users.role will fail with invalid payload', function () {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch("/admin/users/{$otherUser->username}/role", [
            'role' => 'wrong-role'
        ]);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('role');
});

test('admin.users.role will properly update role of user', function () {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch("/admin/users/{$otherUser->username}/role", [
            'role' => UserRole::USER->value
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('users', [
        'username' => $otherUser->username,
        'role' => UserRole::USER->value
    ]);
});
