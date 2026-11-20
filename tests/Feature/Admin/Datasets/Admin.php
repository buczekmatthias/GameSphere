<?php

use App\Enum\UserRole;

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
