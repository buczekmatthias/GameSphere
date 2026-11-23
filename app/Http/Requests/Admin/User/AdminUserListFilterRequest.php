<?php

namespace App\Http\Requests\Admin\User;

use App\Http\Requests\Admin\SortableListRequest;

class AdminUserListFilterRequest extends SortableListRequest
{
    protected function sortableColumns(): array
    {
        return ['name', 'username', 'role', 'email', 'verified', 'avatar', 'created_at'];
    }

    protected function defaultColumn(): string
    {
        return 'role';
    }
}
