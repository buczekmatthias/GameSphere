<?php

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Str;

test('admin.genres.destroy properly deletes genre', function () {
    $genre = Genre::factory()->create();

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->delete("/admin/genres/{$genre->slug}");

    $response->assertRedirect();

    $this->assertDatabaseCount('genres', 0);
});

test('admin.genres.destroy returns 404 for missing entry', function () {
    $uuid = Str::uuid();

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->delete("/admin/genres/{$uuid}");

    $response->assertNotFound();
});
