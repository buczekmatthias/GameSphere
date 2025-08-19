<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Discussion\ListDiscussionResource;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscussionController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke(Request $request)
	{
		$entries = Discussion::with(['author', 'discussable'])->withCount(['comments', 'reports']);
		$column = strtolower($request->get('column', 'title'));
		$order = strtolower($request->get('order', 'asc'));

		if (!in_array($order, ['asc', 'desc'])) {
			$order = 'asc';
		}
		if (!in_array($column, ['title', 'author', 'comments', 'reports', 'created_at'])) {
			$column = 'title';
		}

		match ($column) {
			'title', 'created_at' => $entries->orderBy($column, $order),
			'author' => $entries->join('users', 'users.id', '=', 'discussion.user_id')->orderBy('users.name', $order),
			'comments', 'reports' => $entries->orderBy("{$column}_count", $order),
		};

		return Inertia::render('admin/Discussion', [
			'discussions' => ListDiscussionResource::collection($entries->paginate(50)),
		]);
	}
}
