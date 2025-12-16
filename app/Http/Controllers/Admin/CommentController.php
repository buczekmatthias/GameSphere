<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Comment\AdminCommentListResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
	private const SORT_COLUMNS = ['content', 'discussion', 'user', 'reports', 'created_at'];

	/**
	 * Handle the incoming request.
	 */
	public function __invoke(Request $request): Response
	{
		$entries = Comment::with(['user', 'discussion'])->withCount(['reports']);
		$column = strtolower($request->get('column', 'content'));
		$order = strtolower($request->get('order', 'asc'));

		if (!in_array(strtolower($order), self::ORDER)) {
			$order = 'asc';
		}
		if (!in_array($column, self::SORT_COLUMNS)) {
			$column = 'content';
		}

		match ($column) {
			'content', 'created_at' => $entries->orderBy($column, $order),
			'discussion' => $entries->join('discussions', 'discussions.id', '=', 'comments.discussion_id')->orderBy('discussions.title', $order),
			'user' => $entries->join('users', 'users.id', '=', 'comments.user_id')->orderBy('users.name', $order),
			'reports' => $entries->orderBy("reports_count", $order),
		};

		return Inertia::render('admin/Comment', [
			'comments' => AdminCommentListResource::collection($entries->paginate(50)),
		]);
	}
}
