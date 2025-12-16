<?php

namespace App\Http\Controllers;

use App\Http\Requests\Discussion\StoreRequest;
use App\Http\Requests\Discussion\UpdateRequest;
use App\Http\Resources\Discussion\ListDiscussionResource;
use App\Http\Resources\Discussion\ShowDiscussionResource;
use App\Http\Resources\Games\GamesListResource;
use App\Http\Resources\Genre\GenreListResource;
use App\Models\Discussion;
use App\Models\Game;
use App\Models\Genre;
use App\Services\StoreCommentMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DiscussionController extends Controller
{
	public function index(Request $request): Response
	{
		return Inertia::render('app/discussion/Index', [
			'discussions' => Inertia::defer(fn () => ListDiscussionResource::collection(
				Discussion::with(['author', 'discussable'])
					->when(
						$request->has('title'),
						function (Builder $query) use ($request) {
							return $query->whereLike('title', "%{$request->get('title')}%");
						}
					)
					->withCount('comments')
					->orderBy('created_at', 'DESC')
					->orderBy('id', 'ASC')
					->paginate(15)
			))
		]);
	}

	public function create(string $type, string $slug): Response
	{
		$item = null;

		if ($type === 'game') {
			$item = GamesListResource::make(Game::where('slug', $slug)->first());
		} else {
			$item = GenreListResource::make(Genre::where('slug', $slug)->first());
		}

		return Inertia::render('app/discussion/Create', [
			'type' => $type,
			'item' => $item
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request): RedirectResponse
	{
		$userId = Auth::user()->id;
		$data = $request->validated();

		$discussion = (
			$request->post('type') === 'game'
			? Game::where('slug', $data['slug'])
			: Genre::where('slug', $data['slug'])
		)
			->first()
			->discussions()
			->make([
				'slug' => Str::uuid(),
				'title' => $data['title']
			]);

		$discussion->user_id = $userId;
		$discussion->save();

		Storage::makeDirectory("discussions/{$discussion->slug}");

		$comment = $discussion->comments()->make([
			'slug' => Str::uuid(),
			'content' => $data['content']
		]);

		$comment->media = StoreCommentMedia::storeFiles($discussion->slug, $comment->slug, $data['media']);

		$comment->user_id = $userId;

		$comment->save();

		return to_route('discussions.show', ['discussion' => $discussion->slug]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Discussion $discussion): Response
	{
		$discussion->load(['author', 'discussable']);
		$discussion->loadCount('comments');

		return Inertia::render('app/discussion/Show', [
			'discussion' => ShowDiscussionResource::make($discussion),
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Discussion $discussion, UpdateRequest $request): RedirectResponse
	{
		$discussion->update($request->validated());

		return back(303);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Discussion $discussion, Request $request): RedirectResponse
	{
		foreach ($discussion->comments as $comment) {
			foreach ($comment->media as $file) {
				Storage::delete("discussions/{$discussion->slug}/{$comment->slug}/{$file}");
			}
			Storage::delete("discussions/{$discussion->slug}/{$comment->slug}");
		}
		Storage::deleteDirectory("discussions/{$discussion->slug}");

		DB::transaction(function () use ($discussion) {
			$discussion->reports()->delete();

			$discussion->delete();
		});

		if ($request->get('return_back')) {
			return back(303);
		}

		return to_route('discussions.index', status: 303);
	}
}
