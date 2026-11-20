<?php

namespace App\Http\Requests\Admin\Game;

use App\Http\Requests\Admin\SortableListRequest;

class GameListFilterRequest extends SortableListRequest
{
    protected function sortableColumns(): array
    {
        return ['title', 'media', 'genre', 'creator', 'released_at', 'created_at'];
    }

    protected function defaultColumn(): string
    {
        return 'title';
    }
}
