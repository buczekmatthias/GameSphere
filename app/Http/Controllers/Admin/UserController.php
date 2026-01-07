<?php

namespace App\Http\Controllers\Admin;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UpdateRoleRequest;
use App\Http\Resources\Admin\User\AdminUserListResource;
use App\Models\GameCreatorRequest;
use App\Models\User;
use App\Services\UserDeleteServices;
use App\Services\UserPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
	private const SORT_COLUMNS = ['name', 'username', 'role', 'email', 'verified', 'avatar', 'created_at'];

	/**
	 * Handle the incoming request.
	 */
	public function index(): Response
	{
		$entries = User::query();
		$column = strtolower(request()->get('column', 'content'));
		$order = strtolower(request()->get('order', 'asc'));

		if (!in_array(strtolower($order), self::ORDER)) {
			$order = 'asc';
		}
		if (!in_array($column, self::SORT_COLUMNS)) {
			$column = 'role';
		}

		match ($column) {
			'verify' => $entries->orderBy('email_verified_at', $order),
			'role' => $entries->orderInRoleHierarchy($order),
			default => $entries->orderBy($column, $order),
		};

		return Inertia::render('admin/User', [
			'users' => AdminUserListResource::collection($entries->paginate(50)),
			'roles' => array_values(array_filter(array_reverse(array_column(UserRole::cases(), 'value')), fn ($i) => $i !== UserRole::DEVELOPER->value)),
			'roles_user_can_manage' => UserPermissions::getRolesListUserCanManage(request()->user()),
			'game_creator_requests_count' => GameCreatorRequest::count()
		]);
	}

	public function updateRole(User $user, UpdateRoleRequest $request): RedirectResponse
	{
		$user->update([
			'role' => $request->validated()['role']
		]);

		return back(303);
	}

	public function destroy(User $user): RedirectResponse
	{
		UserDeleteServices::deleteUser($user, request()->get('with_relations', false));

		return back(303);
	}
}
