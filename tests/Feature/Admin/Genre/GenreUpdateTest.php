<?php

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Str;

test('admin.genres.update properly updates genre name', function () {
    $genre = Genre::factory()->create(['name' => 'default']);

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(
            route("admin.genres.update", ['genre' => $genre]),
            ['name' => 'custom']
        )
        ->assertRedirect();
});

test('admin.genres.update will fail with wrong payload', function (string $value) {
    $genre = Genre::factory()->create(['name' => 'default']);
    Genre::factory()->create(['name' => 'custom']);

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(
            route("admin.genres.update", ['genre' => $genre]),
            ['name' => $value]
        )
        ->assertRedirect()
        ->assertSessionHasErrors('name');

    $genre->refresh();

    expect($genre->name)->toBe('default');
})->with([
    'empty name' => '',
    'duplicate name' => 'custom'
]);


test('admin.genres.update returns 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(
            route("admin.genres.update", ['genre' => Str::uuid()]),
            ['name' => 'custom']
        )
        ->assertNotFound();
});
