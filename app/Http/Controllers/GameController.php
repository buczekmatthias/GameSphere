<?php

namespace App\Http\Controllers;

use App\Http\Requests\Game\EditRequest;
use App\Http\Requests\Game\StoreRequest;
use App\Http\Resources\Games\EditGameResource;
use App\Http\Resources\Games\GamesListResource;
use App\Http\Resources\Games\ShowGameResource;
use App\Http\Resources\User\SimpleProfileResource;
use App\Models\Game;
use App\Models\Genre;
use App\Models\User;
use App\Services\ManageMedia;
use App\Services\UserGameListsServices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class GameController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): Response | RedirectResponse
	{
		return Inertia::render('app/game/Index', [
			'games' => Inertia::defer(fn () => GamesListResource::collection(
				Game::gamesWithScore()
					->orderBy('title')
					->when(
						request()->has('title'),
						fn (Builder $query) => $query->whereLike('title', "%".request()->get('title')."%")
					)
					->when(
						request()->has('genre'),
						fn (Builder $query) => $query->where('genre_id', Genre::select('id')->where('name', request()->get('genre'))->first()->id)
					)
					->when(
						request()->has('released_after') && request()->has('released_before'),
						function (Builder $query) {
							$released_after = Carbon::createFromFormat('Y-m-d', request()->get('released_after'));
							$released_before = Carbon::createFromFormat('Y-m-d', request()->get('released_before'));

							if ($released_before->isBefore($released_after)) {
								return redirect()->to(
									url()->current() . '?' . http_build_query(array_merge(
										request()->query(),
										['released_before' => request()->get('released_after')]
									))
								);
							}

							return $query->whereBetween('released_at', [$released_after->startOfDay(), $released_before->endOfDay()]);
						}
					)
					->when(
						request()->has('released_after') && !request()->has('released_before'),
						fn (Builder $query) => $query->whereDate('released_at', '>=', request()->get('released_after'))
					)
					->when(
						request()->has('released_before') && !request()->has('released_after'),
						fn (Builder $query) => $query->whereDate('released_at', '<=', request()->get('released_before'))
					)
					->paginate(30)
			)),
			'genres' => Inertia::defer(fn () => Cache::flexible('games_index_genres', [180, 300], fn () => Genre::select('name')->orderBy('name', 'ASC')->pluck('name')))
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): Response
	{
		return Inertia::render('app/game/Create', [
			'genres' => Genre::select(['slug', 'name'])->orderBy('name')->get()
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request): RedirectResponse
	{
		$data = $request->validated();

		$genre = Genre::select(['id'])->where('slug', $data['genre'])->first();

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

		if (Carbon::parse($data['released_at']) > now()->endOfDay()) {
			// Game creator discussion, exclusive to game creator and staff
			$discussion = $game->discussions()->make([
				'slug' => Str::uuid(),
				'title' => 'Game creator updates',
				'is_locked' => true
			]);
			$discussion->author()->associate($request->user());

			$discussion->save();

			// Game discussion, used for users to discuss about it before now() > released_at
			$discussion = $game->discussions()->make([
				'slug' => Str::uuid(),
				'title' => 'Pre-release discussion'
			]);
			$discussion->author()->associate($request->user());

			$discussion->save();
		}

		return to_route('games.show', ['game' => $game->slug]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(string $game, string $tab = 'reviews'): Response
	{
		$g = Game::getGameWithScore()
			->where('slug', $game)
			->with([
				'genre:id,name,slug',
				'creator:id,name,username',
			])
			->withCount(['reviews'])
			->firstOrFail();

		return Inertia::render('app/game/Show', [
			'game' => ShowGameResource::make($g),
			'userLists' => Inertia::defer(fn () => UserGameListsServices::checkIfGameIsInAnyUserGamesList($g), 'lists'),
			'tab' => $tab,
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Game $game): Response
	{
		$game->load([
			'genre:id,slug,name',
			'creator:id,avatar,name,username'
		]);

		return Inertia::render('app/game/Edit', [
			'game' => EditGameResource::make($game),
			'genres' => Genre::select(['slug', 'name'])->orderBy('name', 'ASC')->get(),
			'users' => request()->user()?->isStaff() ? SimpleProfileResource::collection(User::permittedToOwnGame()->get()) : null
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

			ManageMedia::replaceFile($path, $game->thumbnail, $data['thumbnail'], $thumbnailName);

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
			$tempMedia = ManageMedia::updateMedia($path, $game->media, $data['media_to_delete']);
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
		$path = "games/{$game->slug}";
		$media = [...$game->media, $game->thumbnail];

		DB::transaction(function () use ($game) {
			$game->reports()->delete();
			$game->discussions()->delete();
			$game->delete();
		});

		ManageMedia::deleteDirectoryWithMedia($path, $media);

		return to_route('games.index', status: 303);
	}

	public function toggleGameOnList(Game $game, Request $request): RedirectResponse
	{
		$list = $request->validate([
			'list' => ['string', 'required', 'in:'.implode(",", UserGameListsServices::getList($game))]
		])['list'];

		/** @var \App\Models\User */
		$user = $request->user();

		if ($user->games()->where('game_id', $game->id)->wherePivot('list_type', $list)->exists()) {
			DB::table('game_user')
				->where('user_id', $user->id)
				->where('game_id', $game->id)
				->where('list_type', $list)
				->delete();
		} else {
			$user->games()->attach($game, ['list_type' => $list]);
		}

		return back();
	}
}
