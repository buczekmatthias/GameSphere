<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GameCreatorRequestController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\UserReportsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StoreReportController;
use App\Http\Controllers\UserListController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomepageController::class)->name('home');

Route::get('/u/{user:username}/{tab?}', UserProfileController::class)->name('user.profile');

Route::middleware(['auth'])->group(function () {
	Route::get('/my-reports', UserReportsController::class)->name('user.reports');

	Route::post('/become-game-creator', GameCreatorRequestController::class)->middleware('non.game.creator')->name('game.creator.join');

	Route::post('/reports', StoreReportController::class)->name('reports.store');

	Route::post('/games/{game}', [GameController::class, 'update'])->name('games.update');
	Route::post('/games/{game}/toggle-list', [GameController::class, 'toggleGameOnList'])->name('games.lists.toggle');
	Route::resource('games', GameController::class)->middleware(['game.creator'])->except(['index', 'show']);

	Route::post('/genres/{genre}/toggle-favorite', [GenreController::class, 'toggleFavoriteGenre'])->name('genres.favorite');

	Route::get("/games/{game}/reviews/create", [ReviewController::class, 'create'])->name('reviews.create');
	Route::resource('reviews', ReviewController::class)->only(['store', 'destroy']);
	Route::get("/{type}/{slug}/discussions/create", [DiscussionController::class, 'create'])->name('discussions.create')->whereIn('type', ['game', 'genre']);
	Route::resource('discussions', DiscussionController::class)->only(['store', 'update', 'destroy']);
	Route::resource('comments', CommentController::class)->only(['store', 'edit', 'update', 'destroy']);
});

Route::get('/users', UserListController::class)->name('users.index');
Route::resource('games', GameController::class)->only(['index', 'show']);
Route::resource('reviews', ReviewController::class)->only(['show']);
Route::resource('discussions', DiscussionController::class)->only(['index', 'show']);
Route::resource('comments', CommentController::class)->only(['show']);
Route::resource('genres', GenreController::class)->only(['index', 'show']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
