<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Review\AdminReviewListResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
	private const SORT_COLUMNS = ['content', 'user', 'game', 'reports', 'created_at'];

	/**
	 * Handle the incoming request.
	 */
	public function __invoke(Request $request): Response
	{
		$entries = Review::with(['user', 'game'])->withCount(['reports']);
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
			'user' => $entries->join('users', 'users.id', '=', 'reviews.user_id')->orderBy('users.name', $order),
			'game' => $entries->join('games', 'games.id', '=', 'reviews.game_id')->orderBy('games.title', $order),
			'reports' => $entries->orderBy('reports_count', $order),
		};

		return Inertia::render('admin/review/Index', [
			'reviews' => AdminReviewListResource::collection($entries->paginate(50)),
		]);
	}
}
