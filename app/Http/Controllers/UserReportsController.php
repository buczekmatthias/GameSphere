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
				Report::where('user_id', request()->user()->id)
				->with(['reportable'])
				->paginate(25)
			),
		]);
	}
}
