<?php

test('registration screen can be rendered', function () {
    $this
        ->get(route('security.register'))
        ->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post(route('security.register'), [
        'name' => 'Test User',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('home', absolute: false));
});
