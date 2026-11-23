<?php

namespace App\Http\Requests\Admin\Report;

use App\Http\Requests\Admin\SortableListRequest;

class ReportListFilterRequest extends SortableListRequest
{
    protected function sortableColumns(): array
    {
        return ['reason', 'user', 'status', 'created_at'];
    }

    protected function defaultColumn(): string
    {
        return 'created_at';
    }

    protected function defaultOrder(): string
    {
        return 'desc';
    }
}
