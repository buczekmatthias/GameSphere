<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

final class Routes
{
	public static function getRoutes(): array
	{
		/** @var \App\Models\User */
		$currentUser = Auth::user();

		if (!$currentUser) {
			return ['security', 'guest'];
		}

		if ($currentUser->isGameCreator()) {
			return ['security', 'guest', 'user', 'game_creator'];
		}

		if ($currentUser->isModerator()) {
			return ['security', 'guest', 'user', 'game_creator', 'mod'];
		}

		if ($currentUser->isAdmin()) {
			return ['security', 'guest', 'user', 'game_creator', 'mod', 'admin'];
		}

		if ($currentUser->isDeveloper()) {
			return ['security', 'guest', 'user', 'game_creator', 'mod', 'admin', 'dev'];
		}

		if ($currentUser) {
			return ['security', 'guest', 'user'];
		}

		return [];
	}
}
