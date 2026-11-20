<?php

use App\Enum\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {

});

test('admin.dashboard will redirect for guest', function () {
    $response = $this->get("/admin/dashboard");

    $response->assertRedirect(route('security.login'));
});

test('admin.dashboard will only work for staff', function (UserRole $role, int $expectedStatus) {
    $response = $this
        ->actingAs(User::factory()->create(['role' => $role->value]))
        ->get('/admin/dashboard');

    $response->assertStatus($expectedStatus);
})->with([
    'moderator' => [UserRole::MODERATOR, 200],
    'admin' => [UserRole::ADMIN, 200],
    'dev' => [UserRole::DEVELOPER, 200],
    'creator' => [UserRole::GAME_CREATOR, 403],
    'user' => [UserRole::USER, 403],
]);

test('admin.dashboard returns inertia response with stats', function () {
    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN->value]))
        ->get('/admin/dashboard');

    $response
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
