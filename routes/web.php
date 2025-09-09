<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\UserReportsController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StoreReportController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomepageController::class)->name('home');

Route::get('/u/{user:username}/{tab?}', UserProfileController::class)->name('user.profile');
Route::get('/my-reports', UserReportsController::class)->name('user.reports');

Route::post('/reports', StoreReportController::class)->name('reports.store');

Route::post('/games/{game}', [GameController::class, 'update'])->name('games.update');
Route::post('/games/{game}/toggle-list', [GameController::class, 'toggleGameOnList'])->name('games.lists.toggle');
Route::resource('games', GameController::class)->except('update');

Route::resource('reviews', ReviewController::class)->only(['show', 'store', 'destroy']);
Route::resource('discussions', DiscussionController::class)->except(['create', 'edit']);
Route::resource('comments', CommentController::class)->except(['index', 'create', 'edit']);
Route::resource('genres', GenreController::class)->only(['index', 'show']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
