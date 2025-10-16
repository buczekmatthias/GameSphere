<?php

namespace App\Http\Controllers;

use App\Models\GameCreatorRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GameCreatorRequestController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke()
	{
		$user = Auth::user();

		if (!GameCreatorRequest::where('user_id', $user->id)->exists()) {
			$request = GameCreatorRequest::make([
				'slug' => Str::uuid()
			]);

			$request->user()->associate($user);
			$request->save();
		}

		return back();
	}
}
