<?php

namespace App\Http\Controllers;

use App\Http\Requests\Discussion\StoreRequest;
use App\Http\Requests\Discussion\UpdateRequest;
use App\Http\Resources\Discussion\ShowDiscussionResource;
use App\Models\Discussion;
use App\Models\Game;
use App\Models\Genre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DiscussionController extends Controller
{
	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request)
	{
		$discussion = (
			$request->post('type') === 'game'
			? Game::where('slug', $request->post('slug'))
			: Genre::where('slug', $request->post('slug'))
		)
			->first()->discussions()->make([
				'slug' => Str::uuid(),
				'title' => $request->post('title')
			]);

		$discussion->user_id = Auth::user()->id;
		$discussion->save();

		return back();
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Discussion $discussion)
	{
		$discussion->load(['author', 'discussable']);
		$discussion->loadCount('comments');

		return Inertia::render('discussion/Show', [
			'discussion' => ShowDiscussionResource::make($discussion)
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Discussion $discussion, UpdateRequest $request)
	{
		$discussion->title = $request->post('title');
		$discussion->save();

		return back(303);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Discussion $discussion)
	{
		$discussion->delete();

		return back(303);
	}
}
