<?php

namespace App\Http\Controllers;

use App\Http\Requests\Discussion\StoreRequest;
use App\Http\Requests\Discussion\UpdateRequest;
use App\Http\Resources\Discussion\ListDiscussionResource;
use App\Http\Resources\Discussion\ShowDiscussionResource;
use App\Models\Discussion;
use App\Models\Game;
use App\Models\Genre;
use App\Services\UserPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DiscussionController extends Controller
{
	public function index()
	{
		return Inertia::render('app/discussion/Index', [
			'discussions' => Inertia::defer(fn () => ListDiscussionResource::collection(
				Discussion::with(['author', 'discussable'])->withCount('comments')->orderBy('created_at', 'DESC')->paginate(15)
			))
		]);
	}

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

		Storage::makeDirectory("discussions/{$discussion->slug}");

		return back();
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Discussion $discussion, Request $request)
	{
		$discussion->load(['author', 'discussable']);
		$discussion->loadCount('comments');

		return Inertia::render('app/discussion/Show', [
			'discussion' => ShowDiscussionResource::make($discussion),
			'permissions' => [
				'update' => UserPermissions::checkPermissions('update', $discussion),
				'destroy' => UserPermissions::checkPermissions('delete', $discussion),
			]
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Discussion $discussion, UpdateRequest $request)
	{
		$discussion->update($request->validated());

		return back(303);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Discussion $discussion, Request $request)
	{
		foreach ($discussion->comments as $comment) {
			foreach ($comment->media as $file) {
				Storage::delete("discussions/{$discussion->slug}/{$comment->slug}/{$file}");
			}
			Storage::delete("discussions/{$discussion->slug}/{$comment->slug}");
		}
		Storage::deleteDirectory("discussions/{$discussion->slug}");

		$discussion->reports()->delete();

		$discussion->delete();

		if ($request->get('return_back')) {
			return back(303);
		}

		return to_route('discussions.index', status: 303);
	}
}
