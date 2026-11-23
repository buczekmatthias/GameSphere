<?php

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\User;

test('admin.genres.update properly updates genre name', function () {
    $genre = Genre::factory()->create(['name' => 'default']);

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch("/admin/genres/{$genre->slug}", [
            'name' => 'custom'
        ]);

    $response->assertRedirect();
});

test('admin.genres.update will fail with wrong payload', function (string $value) {
    $genre = Genre::factory()->create(['name' => 'default']);
    Genre::factory()->create(['name' => 'custom']);

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch("/admin/genres/{$genre->slug}", [
            'name' => $value
        ]);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('name');

    $genre->refresh();

    expect($genre->name)->toBe('default');
})->with([
    'empty name' => '',
    'duplicate name' => 'custom'
]);
