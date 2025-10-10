<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserProfileResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserProfileController extends Controller
{
	public function __invoke(Request $request, User $user, string $tab = 'created_games'): Response | RedirectResponse
	{
		$tabs = ['created_games', 'games', 'genres', 'reviews', 'discussions', 'comments'];

		if (!in_array($tab, $tabs)) {
			return to_route('user.profile');
		}

		return Inertia::render('app/UserProfile', [
			'user' => UserProfileResource::make($user),
			'isCurrentUserProfile' => $request->user() ? $user->username === $request->user()->username : false,
			'tabs' => $tabs,
			'activeTab' => $tab
		]);
	}
}
