<?php

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\User;

test('admin.genres.store will store new genre properly', function () {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->post("/admin/genres", [
            'name' => 'new genre'
        ]);

    $response->assertRedirect(route('admin.genres.index'));

    $this->assertDatabaseCount('genres', 1);
});

test('admin.genres.store will fail with invalid payload', function (string $value) {
    if ($value) {
        Genre::factory()->create(['name' => $value]);

        $this->assertDatabaseCount('genres', 1);
    }

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->from(route('admin.genres.index'))
        ->post("/admin/genres", [
            'name' => $value
        ]);

    $response
        ->assertRedirect(route('admin.genres.index'))
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('genres', $value ? 1 : 0);
})->with([
    'empty name' => '',
    'duplicate name' => 'default'
]);
