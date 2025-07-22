<?php

namespace App\Http\Controllers;

use App\Http\Resources\Report\UserReportsTableResource;
use App\Models\Report;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke(Request $request)
	{
		return Inertia::render('app/Reports', [
			'reports' => UserReportsTableResource::collection(
				Report::where('user_id', $request->user()->id)
				->with(['reportable'])
				->get()
			),
		]);
	}
}
