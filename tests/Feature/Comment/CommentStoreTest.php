<?php

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function validCommentStorePayload(array $override = []): array
{
    return array_merge(
        [
            'discussion_slug' => Discussion::factory()->create()->slug,
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
    $this
        ->post(route("comments.store"), validCommentStorePayload())
        ->assertRedirect(route('security.login'));
});

test('comments.store successfully stores new comment and redirects to discussion page', function () {
    Storage::fake('public');
    
    $payload = validCommentStorePayload();
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route("comments.store"), $payload)
        ->assertRedirect(route("discussions.show", ['discussion' => $payload['discussion_slug']]));

    $comment = Comment::query()->firstOrFail();

    $this->assertDatabaseHas('comments', [
        'content' => $payload['content'],
        'user_id' => $user->id
    ]);

    foreach ($comment->media as $file) {
        Storage::disk('public')->assertExists("discussions/{$payload['discussion_slug']}/{$comment->slug}/{$file}");
    }
});

test('comment.store fails with wrong payload', function (string $key, mixed $value) {
    Storage::fake('public');
    
    $payload = validCommentStorePayload([$key => $value]);

    $this->assertDatabaseCount('comments', 0);

    $this
        ->actingAs(User::factory()->create())
        ->post(route("comments.store"), $payload)->assertRedirect()
        ->assertSessionHasErrors();

    $this->assertDatabaseCount('comments', 0);
    
    Storage::disk('public')->assertMissing("discussions/{$payload['discussion_slug']}");
})->with([
    'bad content' => ['content', ''],
    'not valid discussion slug' => ['discussion_slug', 'not-a-uuid'],
    'to much media' => ['media', ['image-1.png', 'image-2.png', 'image-3.png', 'image-4.png', 'image-5.png', 'image-6.png', 'image-7.png']],
    'wrong media format' => ['media', ['note.txt']],
    'media too big' => ['media', [UploadedFile::fake()->image('image.png')->size(104857605)]]
]);
