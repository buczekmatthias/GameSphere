<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\UpdateReportStatusRequest;
use App\Http\Resources\Report\UserReportsTableResource;
use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request)
	{
		$entries = Report::with(['reportable', 'user']);
		$column = strtolower($request->get('column', 'content'));
		$order = strtolower($request->get('order', 'desc'));

		if (!in_array($order, ['asc', 'desc'])) {
			$order = 'desc';
		}
		if (!in_array($column, ['reason', 'user', 'status', 'created_at'])) {
			$column = 'created_at';
		}

		match ($column) {
			'user' => $entries->join('users', 'users.id', '=', 'reports.user_id')->orderBy('users.name', $order),
			default => $entries->orderBy($column, $order),
		};

		return Inertia::render('admin/Report', [
			'reports' => UserReportsTableResource::collection($entries->paginate(50))
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(UpdateReportStatusRequest $request, Report $report)
	{
		$report->update([
			'status' => $request->validated()['status']
		]);

		return back(303);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Report $report)
	{
		$report->delete();

		return back(303);
	}
}
