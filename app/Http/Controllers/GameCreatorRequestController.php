<?php

namespace App\Http\Controllers;

use App\Models\GameCreatorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GameCreatorRequestController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke(): RedirectResponse
	{
		if (!GameCreatorRequest::where('user_id', request()->user()->id)->exists()) {
			$request = GameCreatorRequest::make([
				'slug' => Str::uuid()
			]);

			$request->user()->associate(request()->user());
			$request->save();
		}

		return back();
	}
}
