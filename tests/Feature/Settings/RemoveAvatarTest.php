<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('settings.profile.update.picture.remove redirects guest to login page', function () {
    $this
        ->post(route("settings.profile.update.picture.remove"))
        ->assertRedirect(route("security.login"));
});

test('settings.profile.update.picture.remove removes avatar and redirects to settings.profile.edit', function () {
    Storage::fake('public');

    $name = putFakeFile("users");
    $user = User::factory()->create(['avatar' => $name]);
    
    Storage::disk('public')->assertExists("users/{$name}");

    $this
        ->actingAs($user)
        ->post(route("settings.profile.update.picture.remove"))
        ->assertRedirect(route("settings.profile.edit"))
        ->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->avatar)->toBe(null);

    Storage::disk('public')->assertMissing("users/{$name}");
});
