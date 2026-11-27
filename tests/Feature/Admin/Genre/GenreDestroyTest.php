<?php

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Str;

test('admin.genres.destroy properly deletes genre', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->delete(route("admin.genres.destroy", ['genre' => Genre::factory()->create()]))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseCount('genres', 0);
});

test('admin.genres.destroy returns 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->delete(route("admin.genres.destroy", ['genre' => Str::uuid()]))
        ->assertNotFound();
});
