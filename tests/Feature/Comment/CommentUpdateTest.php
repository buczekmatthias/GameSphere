<?php

use App\Enum\UserRole;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('public');

    $user = User::factory()->create();

    $discussion = Discussion::factory()
        ->for(Genre::factory()->create(), 'discussable')
        ->for($user, 'author')
        ->create();

    $slug = Str::uuid();
    $this->comment = Comment::factory()
        ->for($discussion)
        ->for($user)
        ->create(['slug' => $slug, 'media' => ["discussions/{$discussion->slug}/{$slug}/{$slug}-media-0.png"]]);

    Storage::disk('public')->put("discussions/{$discussion->slug}/{$slug}/{$slug}-media-0.png", 'fake');
});

function validCommentUpdatePayload(Comment $comment, array $override = []): array
{
    return array_merge(
        [
            'content' => 'New content',
            'media_to_delete' => ["{$comment->slug}-media-0.png"]
        ],
        $override
    );
}

test('comments.update redirects to login page for guest', function () {
    $response = $this->put("/comments/{$this->comment->slug}", validCommentUpdatePayload($this->comment));

    $response->assertRedirect(route('security.login'));
});

test('comments.update passes only for owner or staff', function (string $actor, int $expectedStatus) {
    $user = match($actor) {
        'owner' => $this->comment->user,
        'moderator' => User::factory()->create(['role' => UserRole::MODERATOR]),
        'admin' => User::factory()->create(['role' => UserRole::ADMIN]),
        'dev' => User::factory()->create(['role' => UserRole::DEVELOPER]),
        'creator' => User::factory()->create(['role' => UserRole::GAME_CREATOR]),
        'user' => User::factory()->create(['role' => UserRole::USER]),
    };

    $response = $this->actingAs($user)->put("/comments/{$this->comment->slug}", validCommentUpdatePayload($this->comment));

    $response->assertStatus($expectedStatus);
})->with([
    'owner can' => ['owner', 302],
    'moderator can' => ['moderator', 302],
    'admin can' => ['admin', 302],
    'dev can' => ['dev', 302],
    'non-owner creator cannot' => ['creator', 403],
    'regular user cannot' => ['user', 403],
]);

test('comments.update fails with wrong payload', function (string $key, mixed $value) {
    $response = $this->actingAs($this->comment->user)->put("/comments/{$this->comment->slug}", validCommentUpdatePayload($this->comment, [$key => $value]));

    $response
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'bad content' => ['content', ''],
    'wrong media_to_delete format' => ['media_to_delete', 'file.txt'],
]);

test('comments.update properly alters comment', function () {
    $response = $this->actingAs($this->comment->user)->put("/comments/{$this->comment->slug}", validCommentUpdatePayload($this->comment));

    $response
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->comment->refresh();

    $this->assertDatabaseHas('comments', [
        'content' => 'New content'
    ]);

    Storage::disk('public')->assertMissing("discussions/{$this->comment->discussion->slug}/{$this->comment->slug}/{$this->comment->slug}-media-0.png");
});
