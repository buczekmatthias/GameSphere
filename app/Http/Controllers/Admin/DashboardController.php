<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DashboardController extends Controller
{
	/**
	 * Handle the incoming request.
	 */
	public function __invoke()
	{
		return Inertia::render('admin/Dashboard', [
		]);
	}
}
