<?php

use App\Enum\UserRole;
use App\Models\Report;
use App\Models\User;

test('admin.reports.destroy will only pass for admin or developer', function (UserRole $role, int $expectedStatus) {
    $report = Report::factory()->create();

    $response = $this
        ->actingAs(User::factory()->create(['role' => $role]))
        ->delete("/admin/reports/{$report->slug}");

    $response->assertStatus($expectedStatus);
})->with([
    'regular user cannot' => [UserRole::USER, 403],
    'creator cannot' => [UserRole::GAME_CREATOR, 403],
    'moderator cannot' => [UserRole::MODERATOR, 403],
    'admin can' => [UserRole::ADMIN, 302],
    'dev can' => [UserRole::DEVELOPER, 302],
]);

test('admin.reports.destroy properly removes report and redirects', function () {
    $report = Report::factory()->create();
    $this->assertDatabaseCount('reports', 1);

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->delete("/admin/reports/{$report->slug}");

    $response->assertRedirect();

    $this->assertDatabaseCount('reports', 0);
});
