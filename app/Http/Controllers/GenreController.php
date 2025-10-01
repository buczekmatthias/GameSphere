<?php

namespace App\Http\Controllers;

use App\Http\Resources\Genre\ListResource;
use App\Http\Resources\Genre\ShowResource;
use App\Models\Genre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GenreController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		return Inertia::render('app/genre/Index', [
			'genres' => ListResource::collection(
				Genre::withCount(['discussions', 'games'])->orderBy('name', 'ASC')->paginate(30),
			)
		]);
	}

	/**
	 * Display the specified resource.
	 */
	public function show(Genre $genre)
	{
		return Inertia::render('app/genre/Show', [
			'genre' => ShowResource::make($genre)
		]);
	}

	public function toggleFavoriteGenre(Genre $genre, Request $request)
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
