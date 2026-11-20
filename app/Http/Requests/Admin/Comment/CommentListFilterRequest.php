<?php

namespace App\Http\Requests\Admin\Comment;

use App\Http\Requests\Admin\SortableListRequest;

class CommentListFilterRequest extends SortableListRequest
{
    public function sortableColumns(): array
    {
        return ['content', 'discussion', 'user', 'reports', 'created_at'];
    }
}
