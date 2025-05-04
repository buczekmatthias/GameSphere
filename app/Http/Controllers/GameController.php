<?php

namespace App\Http\Controllers;

use App\Http\Requests\Game\StoreRequest;
use App\Models\Game;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;

class GameController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(): Response
	{
		return Inertia::render('game/Index', [
			'games' => Game::orderBy('title')->paginate(50)
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): Response
	{
		return Inertia::render('game/Create', [
			'genres' => Genre::select(['slug', 'name'])->get()
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(StoreRequest $request)
	{
		// dd($request->validated());

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
		$game->load(['genre', 'creator']);

		return Inertia::render('game/Show', [
			'game' => $game,
			'reviews' => Inertia::defer(fn () => $game->reviews()->get()),
			'discussions' => Inertia::defer(fn () => $game->discussions()->with('author')->withCount('comments')->get()),
		]);
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Game $game): Response
	{
		return Inertia::render('game/Edit');
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Game $game, Request $request)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Game $game)
	{
		//
	}
}
