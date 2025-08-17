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
		return Inertia::render('admin/report/Index', [
			'reports' => UserReportsTableResource::collection(
				Report::with(['reportable'])
					->orderBy($request->get('column', 'created_at'), $request->get('order', 'asc'))
					->paginate(50)
			)
		]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Report $report)
	{
		$report->load(['reportable', 'user']);

		return Inertia::render('admin/report/Show', [
			'report' => UserReportsTableResource::make($report)
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

		$report->save();

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
