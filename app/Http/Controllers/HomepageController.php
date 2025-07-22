<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;

class HomepageController extends Controller
{
	public function __invoke(): Response
	{
		return Inertia::render('app/Homepage', [
			'games' => Inertia::defer(fn () => Game::orderBy('released_at', 'DESC')->limit(40)->get())
		]);
	}
}
