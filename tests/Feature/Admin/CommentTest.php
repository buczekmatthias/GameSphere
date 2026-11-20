<?php

use App\Enum\UserRole;
use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Genre;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $user = User::factory()->create();

    Comment::factory(5)
        ->for(
            Discussion::factory()
                ->for(Genre::factory()->create(), 'discussable')
                ->for($user, 'author')
                ->create()
        )
        ->for($user)
        ->create();
});

test('admin.comments.index returns inertia response with list of comments', function () {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get("/admin/comments");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/Comment')
                ->has('comments.data', 5)
        );
});

test('admin.comments.index will redirect when wrong column or order is provided', function (string $key, string $value) {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get("/admin/comments?$key=$value");

    $response
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'wrong column' => ['column', 'wrong'],
    'wrong order' => ['order', 'csa']
]);
