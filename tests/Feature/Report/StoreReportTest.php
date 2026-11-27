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
    $this
        ->post(route("reports.store"), validReportPayload(User::factory()->create()))
        ->assertRedirect(route('security.login'));
});

test('reports.store stores new report and redirects', function () {
    $u = User::factory()->create();
    $u2 = User::factory()->create();
    
    $this
        ->actingAs($u)
        ->post(route("reports.store"), validReportPayload($u2))
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    $this->assertDatabaseHas('reports', [
        'reason' => ReportReason::TROLLING->value,
        'user_id' => $u->id,
        'reportable_type' => "App\\Models\\User",
        'reportable_id' => $u2->id
    ]);
});
