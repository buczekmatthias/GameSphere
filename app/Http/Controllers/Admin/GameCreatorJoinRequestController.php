<?php

namespace App\Http\Controllers\Admin;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\User\GameCreatorRequestResource;
use App\Models\GameCreatorRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GameCreatorJoinRequestController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function index(): Response
	{
		return Inertia::render('admin/GameCreatorRequest', [
			'requests' => GameCreatorRequestResource::collection(
				GameCreatorRequest::with(['user'])
				->orderBy('created_at', 'DESC')
				->paginate(50)
			)
		]);
	}

	public function acceptRequest(User $user): RedirectResponse
	{
		$user->role = UserRole::GAME_CREATOR->value;
		$user->save();
		GameCreatorRequest::where('user_id', $user->id)->first()->delete();

		return back();
	}

	public function rejectRequest(User $user): RedirectResponse
	{
		GameCreatorRequest::where('user_id', $user->id)->first()->delete();

		return back();
	}
}
