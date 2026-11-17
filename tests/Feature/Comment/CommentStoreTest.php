<?php

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');
});

function validCommentPayload(array $override = []): array
{
    $discussion = Discussion::factory()
        ->for(Genre::factory()->create(), 'discussable')
        ->for(User::factory()->create(), 'author')
        ->create();

    return array_merge(
        [
            'discussion_slug' => $discussion->slug,
            'content' => 'Valid content',
            'media' => [
                UploadedFile::fake()->image('image-1.png'),
                UploadedFile::fake()->image('image-2.png'),
                UploadedFile::fake()->image('image-3.png'),
            ]
        ],
        $override
    );
}

test('comments.store redirects to login page for guest', function () {
    $response = $this->post("/comments", validCommentPayload());

    $response->assertRedirect(route('security.login'));
});

test('comments.store successfully stores new comment and redirects to discussion page', function () {
    $payload = validCommentPayload();
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post("/comments", $payload);

    $response->assertRedirect(route("discussions.show", ['discussion' => $payload['discussion_slug']]));

    $comment = Comment::query()->latest()->first();

    $this->assertDatabaseHas('comments', [
        'content' => $payload['content'],
        'user_id' => $user->id
    ]);

    Storage::disk('public')->assertExists("discussions/{$payload['discussion_slug']}/{$comment->slug}/{$comment->slug}-media-0.png");
    Storage::disk('public')->assertExists("discussions/{$payload['discussion_slug']}/{$comment->slug}/{$comment->slug}-media-1.png");
    Storage::disk('public')->assertExists("discussions/{$payload['discussion_slug']}/{$comment->slug}/{$comment->slug}-media-2.png");
});

test('comment.store fails with wrong payload', function (string $key, mixed $value) {
    $payload = validCommentPayload([$key => $value]);

    $this->assertDatabaseCount('comments', 0);

    $response = $this
        ->actingAs(User::factory()->create())
        ->post("/comments", $payload);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors();

    $this->assertDatabaseCount('comments', 0);
})->with([
    'bad content' => ['content', ''],
    'not valid discussion slug' => ['discussion_slug', 'not-a-uuid'],
    'to much media' => ['media', ['image-1.png', 'image-2.png', 'image-3.png', 'image-4.png', 'image-5.png', 'image-6.png', 'image-7.png']],
    'wrong media format' => ['media', ['note.txt']],
    'media too big' => ['media', [UploadedFile::fake()->image('image.png')->size(104857605)]]
]);
