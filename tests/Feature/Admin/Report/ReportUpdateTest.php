<?php

use App\Enum\ReportStatus;
use App\Enum\UserRole;
use App\Models\Report;
use App\Models\User;

test('admin.reports.update properly updates report status and redirects', function () {
    $report = Report::factory()->create(['status' => ReportStatus::OPEN]);

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch("/admin/reports/{$report->slug}", [
            'status' => ReportStatus::CLOSED->value
        ]);

    $response->assertRedirect();

    $report->refresh();

    expect($report->status)->toBe(ReportStatus::CLOSED);
});

test('admin.reports.update will redirect with errors for wrong status value', function () {
    $report = Report::factory()->create();

    $response = $this
        ->actingAs(User::factory()->create(['role' => UserRole::ADMIN]))
        ->patch("/admin/reports/{$report->slug}", [
            'status' => 'not-right'
        ]);

    $response
        ->assertRedirect()
        ->assertSessionHasErrors('status');

    $this->assertDatabaseMissing('reports', [
        'status' => 'not-right'
    ]);
});
