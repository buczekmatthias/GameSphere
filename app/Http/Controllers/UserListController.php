<?php

namespace App\Http\Controllers;

use App\Models\User;
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
			->when($request->has('contains'), function ($query) use ($request) {
				return $query->whereLike('name', "%{$request->get('contains')}%")
					->orWhereLike('username', "%{$request->get('contains')}%");
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
			'per_page' => $per_page
		]);
	}
}
