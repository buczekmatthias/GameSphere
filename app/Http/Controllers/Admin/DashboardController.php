<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\User\GameCreatorRequestResource;
use App\Http\Resources\Report\UserReportsTableResource;
use App\Models\GameCreatorRequest;
use App\Models\Report;
use App\Services\Admin\DashboardStats;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DashboardStats $stats): Response
    {
        return Inertia::render('admin/Dashboard', [
            ...$stats->get(),
            'active_reports' => UserReportsTableResource::collection(Report::activeReports()->with(['reportable', 'user'])->orderBy('created_at', 'DESC')->limit(5)->get()),
            'pending_requests' => GameCreatorRequestResource::collection(GameCreatorRequest::with(['user'])->orderBy('created_at', 'DESC')->limit(5)->get())
        ]);
    }
}
