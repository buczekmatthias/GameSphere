<?php

namespace App\Http\Requests\Admin\Discussion;

use App\Http\Requests\Admin\SortableListRequest;

class DiscussionListFilterRequest extends SortableListRequest
{
    protected function sortableColumns(): array
    {
        return ['title', 'author', 'comments', 'reports', 'created_at'];
    }

    protected function defaultColumn(): string
    {
        return 'title';
    }
}
