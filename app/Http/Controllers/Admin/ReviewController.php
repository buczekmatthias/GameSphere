<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Review\ReviewListResource;
use App\Http\Resources\Admin\Review\ReviewResource;
use App\Http\Resources\Report\UserReportsTableResource;
use App\Models\Review;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReviewController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function index(Request $request)
	{
		$entries = Review::with(['user', 'game'])->withCount(['reports']);
		$column = strtolower($request->get('column', 'content'));
		$order = strtolower($request->get('order', 'asc'));

		if (!in_array($order, ['asc', 'desc'])) {
			$order = 'asc';
		}
		if (!in_array($column, ['content', 'user', 'game', 'reports', 'created_at'])) {
			$column = 'content';
		}

		match ($column) {
			'content', 'created_at' => $entries->orderBy($column, $order),
			'user' => $entries->join('users', 'users.id', '=', 'reviews.user_id')->orderBy('users.name', $order),
			'game' => $entries->join('games', 'games.id', '=', 'reviews.game_id')->orderBy('games.title', $order),
			'reports' => $entries->orderBy('reports_count', $order),
		};

		return Inertia::render('admin/review/Index', [
			'reviews' => ReviewListResource::collection($entries->paginate(50)),
		]);
	}

	public function show(Review $review)
	{
		$review->load(['user', 'game']);

		return Inertia::render('admin/review/Show', [
			'review' => ReviewResource::make($review),
			'reports' => UserReportsTableResource::collection($review->reports()->with(['user'])->paginate(50))
		]);
	}
}
