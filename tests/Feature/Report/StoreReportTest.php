<?php

use App\Enum\ReportReason;
use App\Models\User;

function validReportPayload(User $user): array
{
    return [
        'reason' => ReportReason::TROLLING->value,
        'id' => $user->username,
        'type' => 'user'
    ];
}

test('reports.store redirects for guest', function () {
    $response = $this->post("/reports", validReportPayload(User::factory()->create()));

    $response->assertRedirect(route('security.login'));
});

test('reports.store stores new report and redirects', function () {
    $u = User::factory()->create();
    $u2 = User::factory()->create();
    $response = $this->actingAs($u)->post("/reports", validReportPayload($u2));

    $response->assertRedirect();

    $this->assertDatabaseHas('reports', [
        'reason' => ReportReason::TROLLING->value,
        'user_id' => $u->id,
        'reportable_type' => "App\\Models\\User",
        'reportable_id' => $u2->id
    ]);
});
