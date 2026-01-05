<?php

namespace App\Http\Controllers;

use App\Enum\UserRole;
use App\Http\Resources\User\SimpleProfileResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserListController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke(Request $request): Response
	{
		$per_page = $request->get('per_page', 30);

		$users = User::select(['name', 'username', 'role', 'avatar'])
			->orderBy('name', 'ASC')
			->when($request->has('contains'), function (Builder $query) use ($request) {
				return $query->where(function ($q) use ($request) {
					$q->whereLike('name', "%{$request->get('contains')}%")
						->orWhereLike('username', "%{$request->get('contains')}%");
				});
			})
			->when($request->has('role'), function (Builder $query) use ($request) {
				return $query->where('role', $request->get('role'));
			})
			->paginate($per_page);

		return Inertia::render('app/Users', [
			'users' => Inertia::defer(fn () => SimpleProfileResource::collection($users)),
			'roles' => array_filter(array_reverse(array_column(UserRole::cases(), 'value')), fn ($r) => $r !== UserRole::USER->value),
			'per_page' => $per_page
		]);
	}
}
