<?php

use App\Enum\UserRole;
use App\Models\User;

test('admin.users.role will pass only when current user is role above managed user', function (UserRole $role, int $expectedStatus) {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $this
        ->actingAs(User::factory()->create(['role' => $role]))
        ->patch(
            route("admin.users.role", ['user' => $otherUser]),
            ['role' => UserRole::ADMIN->value]
        )
        ->assertStatus($expectedStatus);

    match($expectedStatus) {
        302 => $this->assertDatabaseHas('users', [
                'username' => $otherUser->username,
                'role' => UserRole::ADMIN->value
            ]),
        default => $this->assertDatabaseMissing('users', [
            'username' => $otherUser->username,
            'role' => UserRole::ADMIN->value
        ])
    };
})->with([
    'mod against mod' => [UserRole::MODERATOR, 403],
    'admin against mod' => [UserRole::ADMIN, 302]
]);

test('admin.users.role will fail with invalid payload', function () {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(
            route("admin.users.role", ['user' => $otherUser]),
            ['role' => 'wrong-role']
        )
        ->assertRedirect()
        ->assertSessionHasErrors('role');
        
    $this->assertDatabaseMissing('users', [
        'username' => $otherUser->username,
        'role' => 'wrong-role'
    ]);
});

test('admin.users.role will properly update role of user', function () {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(
            route("admin.users.role", ['user' => $otherUser]),
            ['role' => UserRole::USER->value]
        )
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'username' => $otherUser->username,
        'role' => UserRole::USER->value
    ]);
});

test('adamin.users.role return 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(
            route("admin.users.role", ['user' => "inexistent-user"]),
            ['role' => UserRole::USER->value]
        )
        ->assertNotFound();
});
