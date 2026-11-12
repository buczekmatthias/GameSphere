<?php

namespace App\Http\Controllers;

use App\Http\Resources\Genre\GenreListResource;
use App\Http\Resources\Genre\ShowGenreResource;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        request()->user()?->load(['genres:slug']);

        return Inertia::render('app/genre/Index', [
            'genres' => Inertia::defer(
                fn () => GenreListResource::collection(
                    Genre::select(['name', 'slug'])
                        ->withCount(['discussions', 'games'])
                        ->when(
                            request()->has('name'),
                            fn (Builder $query) => $query->whereLike('name', "%".request()->get('name')."%")
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
            'genre' => ShowGenreResource::make($genre)
        ]);
    }

    public function toggleFavoriteGenre(Genre $genre): RedirectResponse
    {
        request()->user()->genres()->toggle($genre);

        return back();
    }
}
