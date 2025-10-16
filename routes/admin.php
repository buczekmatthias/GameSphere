<?php

use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiscussionController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\GameCreatorJoinRequestController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
	Route::get('/dashboard', DashboardController::class)->name('dashboard');

	Route::resource('reports', ReportController::class)->only(['index', 'update', 'destroy']);

	Route::get('/games', GameController::class)->name('games.index');

	Route::resource('reviews', ReviewController::class)->only(['index', 'show']);

	Route::get('/users', [UserController::class, 'index'])->name('users.index');
	Route::patch('/users/{user}/role', [UserController::class, 'updateRole'])->name('users.role');
	Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
	Route::get('game-creator-join-requests', [GameCreatorJoinRequestController::class, 'index'])->name('game.creator.join');
	Route::post('game-creator-join-requests/{user}/accept', [GameCreatorJoinRequestController::class, 'acceptRequest'])->name('game.creator.join.accept');
	Route::post('game-creator-join-requests/{user}/reject', [GameCreatorJoinRequestController::class, 'rejectRequest'])->name('game.creator.join.reject');

	Route::get('/discussions', DiscussionController::class)->name('discussions.index');

	Route::get('/comments', CommentController::class)->name('comments.index');

	Route::resource('genres', GenreController::class)->except(['show', 'create']);
});
