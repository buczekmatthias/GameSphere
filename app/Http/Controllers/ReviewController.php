<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Game;
use App\Http\Requests\Review\StoreRequest;
use App\Http\Resources\Games\GamesListResource;
use App\Http\Resources\Games\GameReviewResource;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
	public function show(Review $review): Response
	{
		$review->load(['user', 'game']);

		return Inertia::render('app/review/Show', [
			'review' => GameReviewResource::make($review)
		]);
	}

	public function create(Game $game): Response
	{
		return Inertia::render('app/review/Create', [
			'game' => GamesListResource::make($game)
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request): RedirectResponse
	{
		$review = Game::where('slug', $request->post('game_slug'))->first()->reviews()->make([
			'slug' => Str::uuid(),
			'content' => $request->post('content'),
			'ratings' => $request->post('ratings')
		]);

		$review->user_id = Auth::user()->id;
		$review->save();

		return to_route('games.show', ['game' => $request->post('game_slug')]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Review $review, Request $request): RedirectResponse
	{
		DB::transaction(function () use ($review) {
			$review->reports()->delete();
			$review->delete();
		});

		if ($request->get('to_route')) {
			return to_route($request->get('to_route'), 303);
		}

		return back(303);
	}
}
