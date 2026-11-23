<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Genre\GenreListFilterRequest;
use App\Http\Requests\Admin\Genre\GenreRequest;
use App\Http\Resources\Admin\Genre\EditGenreResource;
use App\Http\Resources\Genre\GenreListResource;
use App\Models\Genre;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Inertia\Response;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GenreListFilterRequest $request): Response
    {
        $filter = $request->validated();

        $entries = Genre::select(['name', 'slug'])
            ->withCount(['discussions', 'games']);

        match ($filter['column']) {
            'name' => $entries->orderBy('name', $filter['order']),
            'discussions', 'games' => $entries->orderBy("{$filter['column']}_count", $filter['order']),
        };

        return Inertia::render('admin/genre/Index', [
            'genres' => GenreListResource::collection($entries->paginate(50)),
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

        return back();
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

        return back();
    }
}
