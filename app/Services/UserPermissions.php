<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Discussion;
use App\Models\Game;
use App\Models\Genre;
use App\Models\Report;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class UserPermissions
{
	public static function checkPermissions(string $permission, Comment|Discussion|Game|Genre|Report|Review|User $resource): bool
	{
		if (!Auth::check()) {
			return false;
		}

		/** @var User */
		$user = Auth::user();

		return $user->can($permission, $resource);
	}
}
