<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Game;
use App\Http\Requests\Review\StoreRequest;
use App\Models\Review;
use Inertia\Inertia;

class ReviewController extends Controller
{
	public function show(Review $review)
	{
		return Inertia::render('app/Review', [
			'review' => $review
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request)
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
	public function destroy(Review $review)
	{
		$review->delete();

		return back(303);
	}
}
