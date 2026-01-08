<?php

namespace App\Http\Controllers;

use App\Http\Resources\Report\UserReportsTableResource;
use App\Models\Report;
use Inertia\Inertia;
use Inertia\Response;

class UserReportsController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke(): Response
	{
		return Inertia::render('app/Reports', [
			'reports' => UserReportsTableResource::collection(
				Report::select(['status', 'reason', 'created_at', 'reportable_id', 'reportable_type'])
				->where('user_id', request()->user()->id)
				->with(['reportable'])
				->paginate(25)
			),
		]);
	}
}
