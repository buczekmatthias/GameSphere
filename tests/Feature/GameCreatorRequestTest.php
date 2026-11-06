<?php

use App\Enum\UserRole;
use App\Models\GameCreatorRequest;
use App\Models\User;
use Illuminate\Support\Str;

test('guest will be redirected to login page', function () {
    $response = $this->post("/become-game-creator");

    $response->assertRedirect(route('security.login'));
});

test('only regular user who did not apply to become game creator yet can apply', function (UserRole $actor, bool $hasApplied, int $status) {
    $user = User::factory()->create(['role' => $actor]);

    if ($hasApplied) {
        $request = GameCreatorRequest::make([
                'slug' => Str::uuid()
            ]);

        $request->user()->associate($user);
        $request->save();

        $this->assertDatabaseCount('game_creator_requests', 1);
    }

    $response = $this->actingAs($user)->post("/become-game-creator");

    $response->assertStatus($status);

    $this->assertDatabaseCount('game_creator_requests', match($actor) {
        UserRole::USER => 1,
        default => 0
    });
})->with([
    'user_applied' => [UserRole::USER, true, 403],
    'user' => [UserRole::USER, false, 302],
    'game creator' => [UserRole::GAME_CREATOR, false, 403],
    'moderator' => [UserRole::MODERATOR, false, 403],
    'admin' => [UserRole::ADMIN, false, 403],
    'developer' => [UserRole::DEVELOPER, false, 403],
]);
