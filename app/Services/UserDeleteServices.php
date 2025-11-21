<?php

namespace App\Services;

use App\Models\GameCreatorRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class UserDeleteServices
{
	public static function deleteUser(User $user, bool $with_relations = false): void
	{
		DB::transaction(function () use ($user, $with_relations) {
			$pfp = $user->avatar;

			if ($with_relations) {
				$user->games()->delete();
				$user->reviews()->delete();
				$user->discussions()->delete();
				$user->comments()->delete();
			} else {
				$user->games()->update(['user_id' => null]);
				$user->reviews()->update(['user_id' => null]);
				$user->discussions()->update(['user_id' => null]);
				$user->comments()->update(['user_id' => null]);
			}

			GameCreatorRequest::where('user_id', $user->id)->first()?->delete();
			$user->delete();

			if ($pfp) {
				Storage::delete("users/{$pfp}");
			}
		});
	}
}
