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
	public function __invoke(): Response
	{
		$users = User::select(['name', 'username', 'role', 'avatar'])
			->orderBy('name', 'ASC')
			->when(
				request()->has('contains'),
				fn (Builder $query) => $query->where(
					fn ($q) => $q->whereLike('name', "%".request()->get('contains')."%")
						->orWhereLike('username', "%".request()->get('contains')."%")
				)
			)
			->when(
				request()->has('role'),
				fn (Builder $query) => $query->where('role', request()->get('role'))
			)
			->paginate(30);

		return Inertia::render('app/Users', [
			'users' => Inertia::defer(fn () => SimpleProfileResource::collection($users)),
			'roles' => array_filter(array_reverse(array_column(UserRole::cases(), 'value')), fn ($r) => $r !== UserRole::USER->value),
		]);
	}
}
