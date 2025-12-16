<?php

namespace App\Http\Controllers;

use App\Http\Resources\Genre\ListResource;
use App\Http\Resources\Genre\ShowResource;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GenreController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request): Response
	{
		$user = $request->user();

		if ($user) {
			$user->load(['genres']);
		}

		return Inertia::render('app/genre/Index', [
			'genres' => Inertia::defer(
				fn () => ListResource::collection(
					Genre::withCount(['discussions', 'games'])
						->when(
							$request->has('name'),
							function (Builder $query) use ($request) {
								return $query->whereLike('name', "%{$request->get('name')}%");
							}
						)
						->orderBy('name', 'ASC')
						->paginate(30),
				)
			)
		]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Genre $genre): Response
	{
		return Inertia::render('app/genre/Show', [
			'genre' => ShowResource::make($genre)
		]);
	}

	public function toggleFavoriteGenre(Genre $genre, Request $request): RedirectResponse
	{
		/** @var \App\Models\User */
		$user = $request->user();

		if ($user->genres()->where('genre_id', $genre->id)->exists()) {
			$user->genres()->detach($genre);
		} else {
			$user->genres()->attach($genre);
		}

		return back();
	}
}
