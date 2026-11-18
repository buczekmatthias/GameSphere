<?php

use App\Enum\UserRole;
use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('public');

    $discussion = Discussion::factory()
        ->for(Genre::factory()->create(), 'discussable')
        ->for(User::factory()->create(), 'author')
        ->create();

    $slug = Str::uuid();

    $discussion->comments()->create([
        'slug' => $slug,
        'content' => 'Comment content',
        'media' => ["discussions/{$discussion->slug}/{$slug}/{$slug}-media-0.png"]
    ]);

    Storage::disk('public')->put("discussions/{$discussion->slug}/{$slug}/{$slug}-media-0.png", 'fake');

    $this->discussion = $discussion;
});

test('discussions.destroy will redirect guest to login page', function () {
    $response = $this->delete("/discussions/{$this->discussion->slug}");

    $response->assertRedirect(route("security.login"));
});

test('discussions.destroy will pass only for owner or staff', function (string $actor, int $expectedStatus) {
    $user = match($actor) {
        'owner' => $this->discussion->author,
        'moderator' => User::factory()->create(['role' => UserRole::MODERATOR]),
        'admin' => User::factory()->create(['role' => UserRole::ADMIN]),
        'dev' => User::factory()->create(['role' => UserRole::DEVELOPER]),
        'creator' => User::factory()->create(['role' => UserRole::GAME_CREATOR]),
        'user' => User::factory()->create(['role' => UserRole::USER]),
    };

    $response = $this
        ->actingAs($user)
        ->delete("/discussions/{$this->discussion->slug}");

    $response->assertStatus($expectedStatus);
})->with([
    'owner can' => ['owner', 302],
    'moderator can' => ['moderator', 302],
    'admin can' => ['admin', 302],
    'dev can' => ['dev', 302],
    'non-owner creator cannot' => ['creator', 403],
    'regular user cannot' => ['user', 403],
]);

test('discussions.destroy will remove discussion and related files', function () {
    $response = $this
       ->actingAs($this->discussion->author)
       ->delete("/discussions/{$this->discussion->slug}");

    $response->assertRedirect();

    $this->assertDatabaseEmpty('discussions');

    Storage::disk('public')->assertMissing("discussions/{$this->discussion->slug}");
});
