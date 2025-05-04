<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\UserProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomepageController::class)->name('home');

Route::get('/u/{user:username}', UserProfileController::class)->name('user.profile');

Route::resource('games', GameController::class);

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
