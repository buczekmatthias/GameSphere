<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
	Route::get('/dashboard', DashboardController::class)->name('dashboard');

	Route::resource('reports', ReportController::class)->only(['index', 'update', 'destroy']);

	Route::get('/games', GameController::class)->name('games.index');

	Route::resource('reviews', ReviewController::class)->only(['index', 'show']);
});
