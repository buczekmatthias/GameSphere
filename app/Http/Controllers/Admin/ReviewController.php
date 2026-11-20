<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Review\ReviewListFilterRequest;
use App\Http\Resources\Admin\Review\AdminReviewListResource;
use App\Models\Review;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(ReviewListFilterRequest $request): Response
    {
        $filter = $request->validated();

        $entries = Review::select(['slug', 'content', 'created_at', 'user_id', 'game_id', 'ratings'])
            ->with(['user:id,name,username,avatar', 'game:id,title,slug'])
            ->withCount(['reports']);

        match ($filter['column']) {
            'content', 'created_at' => $entries->orderBy($filter['column'], $filter['order']),
            'user' => $entries->join('users', 'users.id', '=', 'reviews.user_id')->orderBy('users.name', $filter['order']),
            'game' => $entries->join('games', 'games.id', '=', 'reviews.game_id')->orderBy('games.title', $filter['order']),
            'reports' => $entries->orderBy('reports_count', $filter['order']),
        };

        return Inertia::render('admin/Review', [
            'reviews' => AdminReviewListResource::collection($entries->paginate(50)),
        ]);
    }
}
