<?php

use App\Enum\UserRole;
use App\Models\Genre;
use App\Models\Report;
use App\Models\User;

function adminUrl(string $routeName, array $models): string
{
    $params = collect($models)
        ->mapWithKeys(fn (string $name) => [$name => match ($name) {
            'user' => User::factory()->create(),
            'genre' => Genre::factory()->create(),
            'report' => Report::factory()->create(),
        }])
        ->all();

    return route($routeName, $params);
}

dataset('admin routes', [
    'comments.index' => ['GET', 'admin.comments.index', []],
    'dashboard' => ['GET','admin.dashboard', []],
    'discussions.index' => ['GET', 'admin.discussions.index', []],
    'join.requests.index' => ['GET', 'admin.game.creator.join', []],
    'games.index' => ['GET', 'admin.games.index', []],
    'genres.index' => ['GET', 'admin.genres.index', []],
    'genres.store' => ['POST', 'admin.genres.store', []],
    'genres.edit' => ['GET', 'admin.genres.edit', ['genre']],
    'genres.update' => ['PUT', 'admin.genres.update', ['genre']],
    'genres.destroy' => ['DELETE', 'admin.genres.destroy', ['genre']],
    'reports.index' => ['GET', 'admin.reports.index', []],
    'reports.update' => ['PUT', 'admin.reports.update', ['report']],
    'reports.destroy' => ['DELETE', 'admin.reports.destroy', ['report']],
    'reviews.index' => ['GET', 'admin.reviews.index', []],
    'users.index' => ['GET', 'admin.users.index', []],
    'users.destroy' => ['DELETE', 'admin.users.destroy', ['user']],
]);

dataset('non-staff roles', [
    'regular user' => UserRole::USER,
    'game creator' => UserRole::GAME_CREATOR,
]);


test('admin routes redirect guests to login', function (string $method, string $routeName, array $models) {
    $this
        ->call($method, adminUrl($routeName, $models))
        ->assertRedirect(route('security.login'));
})->with('admin routes');

test('admin routes are forbidden for non-staff', function (string $method, string $routeName, array $models, UserRole $role) {
    $this
        ->actingAs(User::factory()->create(['role' => $role]))
        ->call($method, adminUrl($routeName, $models))
        ->assertForbidden();
})->with('admin routes')->with('non-staff roles');
