<?php

namespace App\Http\Controllers;

use App\Enum\GameCollectionType;
use App\Http\Requests\Game\EditRequest;
use App\Http\Requests\Game\StoreRequest;
use App\Http\Resources\Games\DiscussionResource;
use App\Http\Resources\Games\EditGameResource;
use App\Http\Resources\Games\GamesListResource;
use App\Http\Resources\Games\ShowGameResource;
use App\Http\Resources\Games\ReviewResource;
use App\Http\Resources\User\SimpleProfileResource;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use App\Services\UserGameListsServices;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class GameController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request): Response | RedirectResponse
	{
		$games = Game::orderBy('title');
		$per_page = $request->get('per_page', 30);

		if ($request->get('title')) {
			$games->whereLike('title', "%{$request->get('title')}%");
		}

		if ($request->has('released_after') && $request->has('released_before')) {
			$released_after = Carbon::createFromFormat('Y-m-d', $request->get('released_after'));
			$released_before = Carbon::createFromFormat('Y-m-d', $request->get('released_before'));

			if ($released_before->isBefore($released_after)) {
				return redirect()->to(
					url()->current() . '?' . http_build_query(array_merge(
						request()->query(),
						['released_before' => $request->get('released_after')]
					))
				);
			}

			$games->whereBetween('released_at', [$released_after->startOfDay(), $released_before->endOfDay()]);
		} elseif ($request->has('released_after')) {
			$games->whereDate('released_at', '>=', $request->get('released_after'));
		} elseif ($request->has('released_before')) {
			$games->whereDate('released_at', '<=', $request->get('released_before'));
		}

		if ($request->get('genre')) {
			$games->where('genre_id', Genre::select('id')->where('name', $request->get('genre'))->first()->id);
		}

		return Inertia::render('app/game/Index', [
			'games' => Inertia::defer(fn () => GamesListResource::collection($games->paginate($per_page))),
			'per_page' => $per_page,
			'genres' => Inertia::defer(fn () => Genre::select('name')->orderBy('name', 'ASC')->pluck('name'))
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): Response
	{
		Gate::authorize('create', Game::class);

		return Inertia::render('app/game/Create', [
			'genres' => Genre::select(['slug', 'name'])->get()
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request): RedirectResponse
	{
		$data = $request->validated();

		$genre = Genre::where('slug', $data['genre'])->first();

		$thumbnailName = 'thumbnail.'.$data['thumbnail']->extension();

		$game = $genre->games()->make([...$data, 'slug' => Str::uuid(), 'thumbnail' => $thumbnailName]);
		$game->creator()->associate($request->user());

		$path = "games/{$game->slug}";
		Storage::makeDirectory($path);

		Storage::putFileAs(
			$path,
			$data['thumbnail'],
			$thumbnailName
		);

		$tempMedia = [];

		if (array_key_exists('media', $data)) {
			foreach ($data['media'] as $i => $file) {
				$fileName = "{$game->slug}-media-{$i}.{$file->extension()}";
				Storage::putFileAs($path, $file, $fileName);
				$tempMedia[] = $fileName;
			}

			$game->media = $tempMedia;
		}

		$game->save();

		return to_route('games.show', ['game' => $game->slug]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Game $game): Response
	{
		$game->load(['genre', 'creator'])->loadCount(['reviews']);

		return Inertia::render('app/game/Show', [
			'game' => ShowGameResource::make($game),
			'userLists' => Inertia::defer(fn () => UserGameListsServices::checkIfGameIsInAnyUserGamesList($game)),
			'reviews' => Inertia::defer(fn () => ReviewResource::collection($game->reviews()->with(['user'])->orderBy('created_at', 'DESC')->paginate(30, pageName: 'reviews_page'))),
			'discussions' => Inertia::defer(fn () => DiscussionResource::collection($game->discussions()->with('author')->withCount('comments')->orderBy('created_at', 'DESC')->paginate(30, pageName: 'discussions_page'))),
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Game $game, Request $request): Response
	{
		$game->load(['genre', 'creator']);

		return Inertia::render('app/game/Edit', [
			'game' => EditGameResource::make($game),
			'genres' => Genre::select(['slug', 'name'])->orderBy('name', 'ASC')->get(),
			'users' => $request->user()?->isStaff() ? SimpleProfileResource::collection(User::permittedToOwnGame()->get()) : null
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Game $game, EditRequest $request): RedirectResponse
	{
		$data = $request->validated();

		$path = "games/{$game->slug}";

		if ($data['thumbnail']) {
			$thumbnailName = 'thumbnail.'.$data['thumbnail']->extension();

			Storage::delete("{$path}/{$game->thumbnail}");

			Storage::putFileAs(
				$path,
				$data['thumbnail'],
				$thumbnailName
			);

			$game->thumbnail = $thumbnailName;
		}

		$game->title = $data['title'];
		$game->description = $data['description'];
		$game->genre_id = Genre::select('id')->where('slug', $data['genre'])->first()->id;
		$game->user_id = User::select('id')->where('username', $data['creator'])->first()->id;

		if ($data['released_at']) {
			$game->released_at = $data['released_at'];
		}

		$tempMedia = [];

		if (array_key_exists('media_to_delete', $data)) {
			foreach ($data['media_to_delete'] as $f) {
				Storage::delete("{$path}/{$f}");
			}
			$tempMedia = array_diff($game->media, $data['media_to_delete']);
		}

		if (array_key_exists('media', $data)) {
			$size = sizeof($tempMedia);
			foreach ($data['media'] as $i => $file) {
				$s = $i + $size;
				$fileName = "{$game->slug}-media-{$s}.{$file->extension()}";
				Storage::putFileAs($path, $file, $fileName);
				$tempMedia[] = $fileName;
			}

			$game->media = $tempMedia;
		}

		$game->save();

		return to_route('games.show', ['game' => $game->slug]);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Game $game): RedirectResponse
	{
		Storage::delete("games/{$game->slug}/{$game->thumbnail}");

		if ($game->media) {
			foreach ($game->media as $file) {
				Storage::delete("games/{$game->slug}/{$file}");
			}
		}

		Storage::deleteDirectory("games/{$game->slug}");

		DB::transaction(function () use ($game) {
			$game->reports()->delete();
			$game->discussions()->delete();
			$game->delete();
		});

		return to_route('games.index', status: 303);
	}

	public function toggleGameOnList(Game $game, Request $request): RedirectResponse
	{
		$list = $request->validate([
			'list' => ['string', 'required', 'in:'.implode(",", array_column(GameCollectionType::cases(), 'value'))]
		])['list'];

		/** @var \App\Models\User */
		$user = $request->user();

		if ($user->games()->where('game_id', $game->id)->wherePivot('list_type', $list)->exists()) {
			DB::table('game_user')
				->where('game_id', $game->id)
				->where('list_type', $list)
				->delete();
		} else {
			$user->games()->attach($game, ['list_type' => $list]);
		}

		return back();
	}
}
