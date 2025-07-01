<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\DiscussionController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomepageController::class)->name('home');

Route::get('/u/{user:username}', UserProfileController::class)->name('user.profile');

Route::post('/games/{game}', [GameController::class, 'update'])->name('games.update');
Route::resource('games', GameController::class)->except('update');

Route::resource('reviews', ReviewController::class)->only(['store', 'destroy']);
Route::resource('discussions', DiscussionController::class)->except(['create', 'edit']);
Route::resource('comments', CommentController::class)->only(['store', 'update', 'destroy']);
Route::resource('genres', GenreController::class)->only(['index', 'show']);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
