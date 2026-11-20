<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Discussion\DiscussionListFilterRequest;
use App\Http\Resources\Admin\Discussion\AdminListDiscussionResource;
use App\Models\Discussion;
use Inertia\Inertia;
use Inertia\Response;

class DiscussionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DiscussionListFilterRequest $request): Response
    {
        $filter = $request->validated();

        $entries = Discussion::select(['slug', 'title', 'discussable_id', 'discussable_type', 'created_at', 'user_id'])
            ->with(['author:id,username,name,avatar', 'discussable'])
            ->withCount(['comments', 'reports']);

        match ($filter['column']) {
            'title', 'created_at' => $entries->orderBy($filter['column'], $filter['order']),
            'author' => $entries->join('users', 'users.id', '=', 'discussion.user_id')->orderBy('users.name', $filter['order']),
            'comments', 'reports' => $entries->orderBy("{$filter['column']}_count", $filter['order']),
        };

        return Inertia::render('admin/Discussion', [
            'discussions' => AdminListDiscussionResource::collection($entries->paginate(50)),
        ]);
    }
}
