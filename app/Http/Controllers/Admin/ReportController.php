<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Report\ReportListFilterRequest;
use App\Http\Requests\Report\UpdateReportStatusRequest;
use App\Http\Resources\Report\UserReportsTableResource;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ReportListFilterRequest $request): Response
    {
        $filter = $request->validated();

        $entries = Report::select(['reason', 'reportable_id', 'reportable_type', 'status', 'slug', 'created_at', 'user_id'])
            ->with(['reportable', 'user:id,username,name,avatar']);

        match ($filter['column']) {
            'user' => $entries->join('users', 'users.id', '=', 'reports.user_id')->orderBy('users.name', $filter['order']),
            default => $entries->orderBy($filter['column'], $filter['order']),
        };

        return Inertia::render('admin/Report', [
            'reports' => UserReportsTableResource::collection($entries->paginate(50))
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReportStatusRequest $request, Report $report): RedirectResponse
    {
        $report->update([
            'status' => $request->validated()['status']
        ]);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Report $report): RedirectResponse
    {
        Gate::authorize('delete', $report);

        $report->delete();

        return back();
    }
}
