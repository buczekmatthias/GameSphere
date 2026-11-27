<?php

use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
    $this
        ->post(route("discussions.store"), validDiscussionStorePayload())
        ->assertRedirect(route("security.login"));
});

test('discussions.store will create new discussion and initial comment with valid payload', function () {
    Storage::fake('public');

    $response = $this
        ->actingAs(User::factory()->create())
        ->post(route("discussions.store"), validDiscussionStorePayload());

    $this
        ->assertDatabaseCount('discussions', 1)
        ->assertDatabaseCount('comments', 1);
    
    $d = Discussion::select('id', 'slug')->with('comments')->first();
    $c = $d->comments->first();

    $response->assertRedirect(route('discussions.show', ['discussion' => $d->slug]));

    foreach ($c->media as $file) {
        Storage::disk('public')->assertExists("discussions/{$d->slug}/{$c->slug}/{$file}");
    }
});

test('discussions.store will fail on invalid payload', function (string $key, mixed $value) {
    $this
        ->actingAs(User::factory()->create())
        ->post(route("discussions.store"), validDiscussionStorePayload([$key => $value]))
        ->assertRedirect()
        ->assertSessionHasErrors();

    $this
        ->assertDatabaseCount('discussions', 0)
        ->assertDatabaseCount('comments', 0);
})->with([
    'bad title' => ['title', ''],
    'type out of scope (games/genres)' => ['type', 'notes'],
    'not a valid slug' => ['slug', 'not-a-uuid'],
    'bad content' => ['content', ''],
    'wrong media format' => ['media', ['note.txt']],
    'too many media' => ['media', ['image-1.png', 'image-2.png', 'image-3.png', 'image-4.png', 'image-5.png', 'image-6.png', 'image-7.png']]
]);
