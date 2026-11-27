<?php

use App\Models\Report;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('user.reports redirects for guest', function () {
    $this
        ->get(route("user.reports"))
        ->assertRedirect(route('security.login'));
});

test('user.reports passes for authenticated users and returns list of content reported by user', function () {
    [$user, $user2, $user3] = User::factory(3)->create();
    Report::factory()->create([
        'user_id' => $user->id,
        'reportable_id' => $user2->id
    ]);
    Report::factory()->create([
        'user_id' => $user2->id,
        'reportable_id' => $user3->id
    ]);

    $this
        ->actingAs($user)
        ->get(route("user.reports"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/Reports')
                ->has('reports.data', 1)
        );
});
