<?php

namespace App\Http\Controllers\Admin;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UpdateRoleRequest;
use App\Http\Resources\Admin\User\UserListResource;
use App\Models\GameCreatorRequest;
use App\Models\User;
use App\Services\UserDeleteServices;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function index(Request $request)
	{
		$entries = User::query();
		$column = strtolower($request->get('column', 'content'));
		$order = strtolower($request->get('order', 'asc'));

		if (!in_array($order, ['asc', 'desc'])) {
			$order = 'asc';
		}
		if (!in_array($column, ['name', 'username', 'role', 'email', 'verified', 'avatar', 'created_at'])) {
			$column = 'name';
		}

		match ($column) {
			'verify' => $entries->orderBy('email_verified_at', $order),
			default => $entries->orderBy($column, $order),
		};

		return Inertia::render('admin/User', [
			'users' => UserListResource::collection($entries->paginate(50)),
			'roles' => array_reverse(array_column(UserRole::cases(), 'value')),
			'game_creator_requests_count' => GameCreatorRequest::count()
		]);
	}

	public function updateRole(User $user, UpdateRoleRequest $request)
	{
		$user->update([
			'role' => $request->validated()['role']
		]);

		return back(303);
	}

	public function destroy(User $user)
	{
		UserDeleteServices::deleteUser($user);

		return back(303);
	}
}
