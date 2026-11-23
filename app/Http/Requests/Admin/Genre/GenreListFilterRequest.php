<?php

namespace App\Http\Requests\Admin\Genre;

use App\Http\Requests\Admin\SortableListRequest;

class GenreListFilterRequest extends SortableListRequest
{
    protected function sortableColumns(): array
    {
        return ['name', 'discussions', 'games'];
    }

    protected function defaultColumn(): string
    {
        return 'name';
    }
}
