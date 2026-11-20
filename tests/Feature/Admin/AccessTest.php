<?php

use App\Enum\UserRole;
use App\Models\User;

test('admin routes redirect guests to login', function (string $method, string $routeName, array $models) {
    $response = $this->call($method, adminUrl($routeName, $models));

    $response->assertRedirect(route('security.login'));
})->with('admin routes');

test('admin routes are forbidden for non-staff', function (string $method, string $routeName, array $models, UserRole $role) {
    $response = $this->actingAs(User::factory()->create(['role' => $role]))
        ->call($method, adminUrl($routeName, $models));

    $response->assertForbidden();
})->with('admin routes')->with('non-staff roles');
