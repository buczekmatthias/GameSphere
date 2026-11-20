<?php

namespace App\Http\Requests\Admin\Review;

use App\Http\Requests\Admin\SortableListRequest;

class ReviewListFilterRequest extends SortableListRequest
{
    protected function sortableColumns(): array
    {
        return ['content', 'user', 'game', 'reports', 'created_at'];
    }

    protected function defaultColumn(): string
    {
        return 'content';
    }
}
