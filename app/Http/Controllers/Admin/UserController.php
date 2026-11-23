<?php

namespace App\Http\Controllers\Admin;

use App\Enum\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UpdateRoleRequest;
use App\Http\Requests\Admin\User\AdminUserListFilterRequest;
use App\Http\Resources\Admin\User\AdminUserListResource;
use App\Models\GameCreatorRequest;
use App\Models\User;
use App\Services\UserDeleteServices;
use App\Services\UserPermissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(AdminUserListFilterRequest $request): Response
    {
        $filter = $request->validated();

        $entries = User::select(['name', 'username', 'role', 'email', 'email_verified_at', 'avatar', 'created_at']);

        match ($filter['column']) {
            'verify' => $entries->orderBy('email_verified_at', $filter['order']),
            'role' => $entries->orderInRoleHierarchy($filter['order']),
            default => $entries->orderBy($filter['column'], $filter['order']),
        };

        return Inertia::render('admin/User', [
            'users' => AdminUserListResource::collection($entries->paginate(50)),
            'roles' => array_values(array_filter(array_reverse(array_column(UserRole::cases(), 'value')), fn ($i) => $i !== UserRole::DEVELOPER)),
            'roles_user_can_manage' => UserPermissions::getRolesListUserCanManage(request()->user()),
            'game_creator_requests_count' => GameCreatorRequest::count()
        ]);
    }

    public function updateRole(User $user, UpdateRoleRequest $request): RedirectResponse
    {
        Gate::authorize('updateRole', $user);

        $user->update($request->validated());

        return back();
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        UserDeleteServices::deleteUser($user, request()->get('with_relations', false));

        return back();
    }
}
