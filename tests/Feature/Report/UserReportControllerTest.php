<?php

use App\Enum\ReportReason;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Str;

test('user.reports redirects for guest', function () {
    $response = $this->get("/my-reports");

    $response->assertRedirect(route('security.login'));
});

test('user.reports passes for authenticated users and returns list of content reported by user', function () {
    [$user, $user2, $user3] = User::factory(3)->create();

    $report = $user2->reports()->make([
            'slug' => Str::uuid(),
            'reason' => ReportReason::HATE_SPEECH
        ]);

    $report->user()->associate($user);
    $report->save();

    $report2 = $user3->reports()->make([
            'slug' => Str::uuid(),
            'reason' => ReportReason::TROLLING
        ]);

    $report2->user()->associate($user2);
    $report2->save();

    $response = $this->actingAs($user)->get("/my-reports");

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (AssertableInertia $page) => $page
                ->component('app/Reports')
                ->has('reports.data', 1)
        );
});
