<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Genre\GenreRequest;
use App\Http\Resources\Admin\Genre\EditGenreResource;
use App\Http\Resources\Genre\ListResource;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Inertia\Response;

class GenreController extends Controller
{
	private const SORT_COLUMNS = ['name', 'discussions', 'games'];

	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request): Response
	{
		$entries = Genre::withCount(['discussions', 'games']);
		$column = strtolower($request->get('column', 'name'));
		$order = strtolower($request->get('order', 'asc'));

		if (!in_array($order, self::ORDER)) {
			$order = 'asc';
		}
		if (!in_array($column, self::SORT_COLUMNS)) {
			$column = 'name';
		}

		match ($column) {
			'name' => $entries->orderBy('name', $order),
			'discussions', 'games' => $entries->orderBy("{$column}_count", $order),
		};

		return Inertia::render('admin/genre/Index', [
			'genres' => ListResource::collection($entries->paginate(50)),
		]);
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(GenreRequest $request): RedirectResponse
	{
		Genre::create([
			'slug' => Str::uuid(),
			'name' => $request->validated()['name']
		]);

		return to_route('admin.genres.index');
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Genre $genre): Response
	{
		return Inertia::render('admin/genre/Edit', [
			'genre' => EditGenreResource::make($genre)
		]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(GenreRequest $request, Genre $genre): RedirectResponse
	{
		$genre->update($request->validated());

		return back(303);
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Genre $genre): RedirectResponse
	{
		DB::transaction(function () use ($genre) {
			$genre->games()->update(['genre_id' => null]);
			$genre->discussions()->delete();
			$genre->delete();
		});

		return back(303);
	}
}
