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

function validDiscussionStorePayload(array $override = []): array
{
    return array_merge(
        [
            'title' => 'Discussion title',
            'type' => 'genres',
            'slug' => Genre::factory()->create()->slug,
            'content' => 'Example discussion content',
            'media' => [
                UploadedFile::fake()->image('image-1.png'),
                UploadedFile::fake()->image('image-2.png'),
            ]
        ],
        $override
    );
}

test('discussions.store will redirect guest to login page', function () {
    $response = $this->post("/discussions", validDiscussionStorePayload());

    $response->assertRedirect(route("security.login"));
});

test('discussions.store will create new discussion and initial comment with valid payload', function () {
    $response = $this
        ->actingAs(User::factory()->create())
        ->post("/discussions", validDiscussionStorePayload());

    $this->assertDatabaseCount('discussions', 1);
    $this->assertDatabaseCount('comments', 1);
    
    $d = Discussion::select('id', 'slug')->with('comments')->first();
    $c = $d->comments->first();

    $response->assertRedirect(route('discussions.show', ['discussion' => $d->slug]));

    Storage::disk('public')->assertExists("discussions/{$d->slug}/{$c->slug}/{$c->slug}-media-0.png");
    Storage::disk('public')->assertExists("discussions/{$d->slug}/{$c->slug}/{$c->slug}-media-1.png");
});

test('discussions.store will fail on invalid payload', function (string $key, mixed $value) {
    $response = $this
        ->actingAs(User::factory()->create())
        ->post("/discussions", validDiscussionStorePayload([$key => $value]));

    $response
        ->assertRedirect()
        ->assertSessionHasErrors();

    $this->assertDatabaseCount('discussions', 0);
    $this->assertDatabaseCount('comments', 0);
})->with([
    'bad title' => ['title', ''],
    'type out of scope (games/genres)' => ['type', 'notes'],
    'not a valid slug' => ['slug', 'not-a-uuid'],
    'bad content' => ['content', ''],
    'wrong media format' => ['media', ['note.txt']],
    'too many media' => ['media', ['image-1.png', 'image-2.png', 'image-3.png', 'image-4.png', 'image-5.png', 'image-6.png', 'image-7.png']]
]);
