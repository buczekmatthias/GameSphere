<?php

use App\Enum\UserRole;
use App\Models\User;

test('admin.users.destroy will pass only when current user is role above managed user', function (UserRole $role, int $expectedStatus, int $expectedCount) {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $this
        ->actingAs(User::factory()->create(['role' => $role]))
        ->delete(route("admin.users.destroy", ['user' => $otherUser]))
        ->assertStatus($expectedStatus);

    $this->assertDatabaseCount('users', $expectedCount);

})->with([
    'mod against mod' => [UserRole::MODERATOR, 403, 2],
    'admin against mod' => [UserRole::ADMIN, 302, 1]
]);

test('admin.users.destroy will properly delete user', function () {
    $otherUser = User::factory()->create(['role' => UserRole::MODERATOR]);

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->delete(route("admin.users.destroy", ['user' => $otherUser]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseMissing('users', [
        'username' => $otherUser->username,
    ]);
});

test('adamin.users.role return 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->delete(route("admin.users.destroy", ['user' => "inexistent-user"]))
        ->assertNotFound();
});
