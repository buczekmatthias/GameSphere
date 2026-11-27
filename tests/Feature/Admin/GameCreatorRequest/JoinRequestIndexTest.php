<?php

use App\Enum\UserRole;
use App\Models\GameCreatorRequest;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('admin.game.creator.join returns inertia response with list of requests to acquire game creator role', function () {
    GameCreatorRequest::factory(5)->create();

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.game.creator.join"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/GameCreatorRequest')
                ->has('requests.data', 5)
        );
});
