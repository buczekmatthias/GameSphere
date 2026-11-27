<?php

use App\Enum\ReportStatus;
use App\Enum\UserRole;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Str;

test('admin.reports.update properly updates report status and redirects', function () {
    $report = Report::factory()->create(['status' => ReportStatus::OPEN]);

    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(
            route("admin.reports.update", ['report' => $report]),
            ['status' => ReportStatus::CLOSED->value]
        )
        ->assertRedirect();

    $report->refresh();

    expect($report->status)->toBe(ReportStatus::CLOSED);
});

test('admin.reports.update will redirect with errors for wrong status value', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(
            route("admin.reports.update", ['report' => Report::factory()->create()]),
            ['status' => 'not-right']
        )
        ->assertRedirect()
        ->assertSessionHasErrors('status');

    $this->assertDatabaseMissing('reports', [
        'status' => 'not-right'
    ]);
});

test('admin.reports.update returns 404 for missing entry', function () {
    $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch(
            route("admin.reports.update", ['report' => Str::uuid()]),
            ['status' => ReportStatus::CLOSED->value]
        )
        ->assertNotFound();
});
