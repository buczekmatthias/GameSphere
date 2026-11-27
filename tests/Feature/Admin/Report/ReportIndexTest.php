<?php

use App\Enum\UserRole;
use App\Models\Report;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    Report::factory(5)->create();
});

test('admin.reports.index returns inertia response with list of reports', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.reports.index"))->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('admin/Report')
                ->has('reports.data', 5)
        );
});

test('admin.reports.index will redirect when wrong column or order is provided', function (string $key, string $value) {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->get(route("admin.reports.index", [$key => $value]))
        ->assertRedirect()
        ->assertSessionHasErrors();
})->with([
    'wrong column' => ['column', 'wrong'],
    'wrong order' => ['order', 'csa']
]);
