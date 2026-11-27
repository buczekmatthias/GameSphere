<?php

use App\Enum\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('admin.dashboard returns inertia response with stats', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN->value]))
        ->get(route("admin.dashboard"))
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/Dashboard')
                ->has('new_entries')
                ->has('chart_data')
                ->has('active_reports')
                ->has('pending_requests')
        );
});
