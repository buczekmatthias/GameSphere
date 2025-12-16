<?php

namespace App\Http\Controllers;

use App\Enum\UserRole;
use App\Models\User;
use App\Services\ShorterNumbers;
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
		$per_page = 30;

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
			->paginate($request->get('per_page', $per_page));

		$from = (($users->currentPage() - 1) * $users->perPage()) + 1;
		$to = ($users->currentPage() - 1) * $users->perPage() + $users->count();

		return Inertia::render('app/Users', [
			'users' => Inertia::defer(fn () => [
				'data' => $users->getCollection(),
				'meta' => [
					'current_page' => $users->currentPage(),
					'from' => $from,
					'last_page' => $users->lastPage(),
					'per_page' => $users->perPage(),
					'to' => $to,
					'total' => $users->total(),
				]
			]),
			'users_count' => ShorterNumbers::convertIntToHumanReadable(User::select('username')->count(), 999),
			'roles' => array_filter(array_reverse(array_column(UserRole::cases(), 'value')), fn ($r) => $r !== UserRole::USER->value),
			'per_page' => $per_page
		]);
	}
}
