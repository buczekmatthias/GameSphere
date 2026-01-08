<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Discussion\AdminListDiscussionResource;
use App\Models\Discussion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DiscussionController extends Controller
{
	private const SORT_COLUMNS = ['title', 'author', 'comments', 'reports', 'created_at'];

	/**
	 * Handle the incoming request.
	 */
	public function __invoke(): Response
	{
		$entries = Discussion::select(['slug', 'title', 'discussable_id', 'discussable_type', 'created_at', 'user_id'])
			->with(['author:id,username,name,avatar', 'discussable'])
			->withCount(['comments', 'reports']);
		$column = strtolower(request()->get('column', 'title'));
		$order = strtolower(request()->get('order', 'asc'));

		if (!in_array(strtolower($order), self::ORDER)) {
			$order = 'asc';
		}
		if (!in_array($column, self::SORT_COLUMNS)) {
			$column = 'title';
		}

		match ($column) {
			'title', 'created_at' => $entries->orderBy($column, $order),
			'author' => $entries->join('users', 'users.id', '=', 'discussion.user_id')->orderBy('users.name', $order),
			'comments', 'reports' => $entries->orderBy("{$column}_count", $order),
		};

		return Inertia::render('admin/Discussion', [
			'discussions' => AdminListDiscussionResource::collection($entries->paginate(50)),
		]);
	}
}
