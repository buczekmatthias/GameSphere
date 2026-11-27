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

    $slug = Str::orderedUuid();
    $fileName = Str::orderedUuid().".png";

    $this->comment = Comment::factory()
        ->for($discussion)
        ->for($user)
        ->create(['slug' => $slug, 'media' => [$fileName]]);

    Storage::disk('public')->put("discussions/{$discussion->slug}/{$slug}/{$fileName}", 'fake');
});

function validCommentUpdatePayload(Comment $comment, array $override = []): array
{
    return array_merge(
        [
            'content' => 'New content',
            'media_to_delete' => [collect($comment->media)->first()]
        ],
        $override
    );
}

test('comments.update redirects to login page for guest', function () {
    $comment = Comment::factory()->create();

    $this
        ->patch(route("comments.update", ['comment' => $comment]), validCommentUpdatePayload($comment))
        ->assertRedirect(route('security.login'));
});

test('comments.update passes only for owner or staff', function (UserRole|string $role, int $expectedStatus) {
    Storage::fake('public');

    $slug = Str::orderedUuid();
    $fileName = Str::orderedUuid().".png";
    $comment = Comment::factory()->create(['slug' => $slug, 'media' => [$fileName], 'content' => 'Old content']);

    $user = match($role) {
        'owner' => $comment->user,
        default => User::factory()->create(['role' => $role]),
    };

    $this
        ->actingAs($user)
        ->patch(route("comments.update", ['comment' => $comment]), validCommentUpdatePayload($comment))
        ->assertStatus($expectedStatus)
        ->assertSessionHasNoErrors();
})->with([
    'owner' => ['owner', 302],
    'user' => [UserRole::USER, 403],
    'creator' => [UserRole::GAME_CREATOR, 403],
    'mod' => [UserRole::MODERATOR, 302],
    'admin' => [UserRole::ADMIN, 302],
    'dev' => [UserRole::DEVELOPER, 302],
]);

test('comments.update fails with wrong payload', function (string $key, mixed $value) {
    $comment = Comment::factory()->create();

    $this
        ->actingAs($comment->user)
        ->patch(route("comments.update", ['comment' => $comment]), validCommentUpdatePayload($comment, [$key => $value]))
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'bad content' => ['content', ''],
    'wrong media_to_delete format' => ['media_to_delete', 'file.txt'],
]);

test('comments.update properly alters comment', function () {
    Storage::fake('public');

    $slug = Str::orderedUuid();
    $fileName = Str::orderedUuid().".png";
    $comment = Comment::factory()->create(['slug' => $slug, 'media' => [$fileName], 'content' => 'Old content']);

    $this
        ->actingAs($comment->user)
        ->patch(route("comments.update", ['comment' => $comment]), validCommentUpdatePayload($comment))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $comment->refresh();

    $this->assertDatabaseHas('comments', [
        'content' => 'New content',
    ]);

    expect(count($comment->media))->toBe(0);

    Storage::disk('public')->assertMissing("discussions/{$comment->discussion->slug}/{$comment->slug}/{$fileName}");
});

test('comments.update returns 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create())
        ->patch(route("comments.update", ['comment' => Str::uuid()]))
        ->assertNotFound();
});
