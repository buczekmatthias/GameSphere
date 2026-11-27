<?php

namespace App\Http\Controllers;

use App\Http\Requests\Game\EditRequest;
use App\Http\Requests\Game\IndexRequest;
use App\Http\Requests\Game\StoreRequest;
use App\Http\Resources\Games\EditGameResource;
use App\Http\Resources\Games\GamesListResource;
use App\Http\Resources\Games\ShowGameResource;
use App\Models\Game;
use App\Models\Genre;
use App\Services\UserGameListsServices;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequest $request): Response
    {
        $filters = $request->validated();

        return Inertia::render('app/game/Index', [
            'games' => Inertia::defer(fn () => GamesListResource::collection(
                Game::gamesWithScore()
                    ->orderBy('title')
                    ->when($filters['title'] ?? null, fn (Builder $q, string $title) => $q->whereLike('title', "%{$title}%"))
                    ->when($filters['genre'] ?? null, fn (Builder $q, string $genre) => $q->whereHas('genre', fn (Builder $sq) => $sq->whereRaw('LOWER(name) = ?', [mb_strtolower($genre)])))
                    ->when($filters['released_after'] ?? null, fn (Builder $q, string $date) => $q->whereDate('released_at', '>=', $date))
                    ->when($filters['released_before'] ?? null, fn (Builder $q, string $date) => $q->whereDate('released_at', '<=', $date))
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

        $stored = [];
        $slug = Str::orderedUuid();
        $path = "games/{$slug}";

        try {
            $game = DB::transaction(function () use ($data, $path, $request, $slug, &$stored) {
                $game = Genre::select(['id'])
                    ->where('slug', $data['genre'])
                    ->firstOrFail()
                    ->games()
                    ->make([...$data, 'slug' => $slug, 'thumbnail' => '', 'media' => []]);
                $game->creator()->associate($request->user());

                Storage::makeDirectory($path);
                $name = "thumbnail-".Str::orderedUuid().".{$data['thumbnail']->extension()}";
                Storage::putFileAs($path, $data['thumbnail'], $name);
                $game->thumbnail = $name;
                $stored[] = $name;

                $media = [];

                foreach ($data['media'] ?? [] as $file) {
                    $name = Str::orderedUuid().".{$file->extension()}";
                    Storage::putFileAs($path, $file, $name);
                    $media[] = $name;
                    $stored[] = "{$path}/{$name}";
                }
                $game->media = $media;
                $game->save();

                if (Carbon::parse($data['released_at'])->isAfter(now()->endOfDay())) {
                    // Game creator discussion, exclusive to game creator and staff
                    $discussion = $game->discussions()->make([
                        'slug' => Str::orderedUuid(),
                        'title' => 'Game creator updates',
                        'is_locked' => true
                    ]);
                    $discussion->author()->associate($request->user());
                    $discussion->save();

                    // Game discussion, used for users to discuss about it before now() > released_at
                    $discussion = $game->discussions()->make([
                        'slug' => Str::orderedUuid(),
                        'title' => 'Pre-release discussion'
                    ]);
                    $discussion->author()->associate($request->user());
                    $discussion->save();
                }

                return $game;
            });
        } catch (\Throwable $e) {
            Storage::delete($stored);
            Storage::deleteDirectory($path);
            report($e);

            throw ValidationException::withMessages([
                'media' => 'Media upload failed. Please try again.'
            ]);
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
        Gate::authorize('update', $game);

        $game->load([
            'genre:id,slug,name',
            'creator:id,avatar,name,username'
        ]);

        return Inertia::render('app/game/Edit', [
            'game' => EditGameResource::make($game),
            'genres' => Genre::select(['slug', 'name'])->orderBy('name', 'ASC')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Game $game, EditRequest $request): RedirectResponse
    {
        Gate::authorize('update', $game);

        $data = $request->validated();
        $path = "games/{$game->slug}";
        $obsolete = [];
        $stored = [];

        try {
            DB::transaction(function () use ($data, $game, $path, &$obsolete, &$stored) {
                if (isset($data['thumbnail'])) {
                    $name = "thumbnail-".Str::orderedUuid().".{$data['thumbnail']->extension()}";
                    Storage::putFileAs($path, $data['thumbnail'], $name);
                    $stored[] = "{$path}/{$name}";
                    $obsolete[] = "{$path}/{$game->thumbnail}";

                    $game->thumbnail = $name;
                }

                $game->title = $data['title'];
                $game->description = $data['description'];

                if (isset($data['genre'])) {
                    $game->genre_id = Genre::select('id')->where('slug', $data['genre'])->firstOrFail()->id;
                }

                if (isset($data['released_at'])) {
                    $game->released_at = $data['released_at'];
                }

                $media = $game->media ?? [];
                $toDelete = array_values(array_intersect($data['media_to_delete'] ?? [], $media));
                $media = array_values(array_diff($media, $toDelete));
                $obsolete = array_merge($obsolete, array_map(fn (string $file) => "{$path}/{$file}", $toDelete));

                foreach ($data['media'] ?? [] as $file) {
                    $name = Str::orderedUuid().".{$file->extension()}";
                    Storage::putFileAs($path, $file, $name);
                    $stored[] = "{$path}/{$name}";
                    $media[] = $name;
                }

                $game->media = $media;
                $game->save();
            });
        } catch (\Throwable $e) {
            Storage::delete($stored);
            report($e);

            throw ValidationException::withMessages([
                'media' => 'Media upload failed. Please try again.'
            ]);
        }

        try {
            Storage::delete($obsolete);
        } catch (\Throwable $e) {
            report($e);
        }

        return to_route('games.show', ['game' => $game->slug]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game): RedirectResponse
    {
        Gate::authorize('delete', $game);

        $path = "games/{$game->slug}";

        DB::transaction(function () use ($game) {
            $game->reports()->delete();
            $game->discussions()->delete();
            $game->delete();
        });

        try {
            Storage::deleteDirectory($path);
        } catch (\Throwable $e) {
            report($e);
        }

        return to_route('games.index');
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
