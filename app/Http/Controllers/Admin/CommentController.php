<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Comment\CommentListFilterRequest;
use App\Http\Resources\Admin\Comment\AdminCommentListResource;
use App\Models\Comment;
use Inertia\Inertia;
use Inertia\Response;

class CommentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(CommentListFilterRequest $request): Response
    {
        $filter = $request->validated();

        $entries = Comment::select(['slug', 'content', 'media', 'created_at', 'user_id', 'discussion_id'])
            ->with(['user:id,name,username,avatar', 'discussion:id,title,slug'])
            ->withCount(['reports']);

        match ($filter['column']) {
            'content', 'created_at' => $entries->orderBy($filter['column'], $filter['order']),
            'discussion' => $entries->join('discussions', 'discussions.id', '=', 'comments.discussion_id')->orderBy('discussions.title', $filter['order']),
            'user' => $entries->join('users', 'users.id', '=', 'comments.user_id')->orderBy('users.name', $filter['order']),
            'reports' => $entries->orderBy("reports_count", $filter['order']),
        };

        return Inertia::render('admin/Comment', [
            'comments' => AdminCommentListResource::collection($entries->paginate(50)),
        ]);
    }
}
