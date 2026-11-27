<?php

use App\Models\User;

test('confirm password screen can be rendered', function () {
    $this
        ->actingAs(User::factory()->create())
        ->get(route("security.password.confirm"))
        ->assertStatus(200);
});

test('password can be confirmed', function () {
    $this
        ->actingAs(User::factory()->create())
        ->post(route("security.password.confirm"), [
            'password' => 'password',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
});

test('password is not confirmed with invalid password', function () {
    $this
        ->actingAs(User::factory()->create())
        ->post(route("security.password.confirm"), [
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrors();
});
