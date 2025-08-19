<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class UserDeleteServices
{
	public static function deleteUser(User $user): void
	{
		DB::transaction(function () use ($user) {
			$user->games()->delete();
			$user->reviews()->update(['user_id' => null]);
			$user->discussions()->update(['user_id' => null]);
			$user->comments()->update(['user_id' => null]);
			$user->delete();
		});
	}
}
