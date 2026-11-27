<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('settings.profile.update.picture redirects guest to login page', function () {
    $this
        ->post(route("settings.profile.update.picture"))
        ->assertRedirect(route("security.login"));
});

test('settings.profile.update.picture stores avatar and redirects to settings.profile.edit', function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route("settings.profile.update.picture"), [
            'avatar' => UploadedFile::fake()->image("avatar.png")
        ])
        ->assertRedirect(route("settings.profile.edit"))
        ->assertSessionHasNoErrors();

    $user->refresh();

    expect($user->avatar)->not()->toBe(null);

    Storage::disk('public')->assertExists("users/{$user->avatar}");
});

test("settings.profile.update.picture redirects with error on invalid payload", function (UploadedFile $file) {
    Storage::fake('public');

    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->from(route("settings.profile.edit"))
        ->post(route("settings.profile.update.picture"), [
            'avatar' => $file
        ])
        ->assertRedirect(route("settings.profile.edit"))
        ->assertSessionHasErrors('avatar');

    $user->refresh();

    expect($user->avatar)->toBe(null);

    Storage::disk('public')->assertMissing("users/{$user->avatar}");
})->with([
    'wrong extension' => UploadedFile::fake()->create('note.txt', 250, 'plain/text'),
    'file too big' => UploadedFile::fake()->image('avatar.png')->size(150000)
]);
