<?php

use App\Enum\UserRole;
use App\Models\Comment;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    Comment::factory(5)->create();
});

test('admin.comments.index returns inertia response with list of comments', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.comments.index"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/Comment')
                ->has('comments.data', 5)
        );
});

test('admin.comments.index will redirect when wrong column or order is provided', function (string $key, string $value) {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.comments.index", [$key => $value]))
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'wrong column' => ['column', 'wrong'],
    'wrong order' => ['order', 'csa']
]);
