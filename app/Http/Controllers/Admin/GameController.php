<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Game\GameListFilterRequest;
use App\Http\Resources\Admin\Game\AdminGameListResource;
use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(GameListFilterRequest $request): Response
    {
        $filter = $request->validated();

        $entries = Game::select(['title', 'description', 'media', 'released_at', 'created_at', 'slug', 'user_id', 'genre_id'])
            ->with(['creator:id,username,name,avatar', 'genre:id,name,slug']);

        match ($filter['column']) {
            'title', 'released_at', 'created_at' => $entries->orderBy($filter['column'], $filter['order']),
            'media' => $entries->orderByRaw("json_array_length(media) {filter$['order']}"),
            'genre' => $entries->join('genres', 'genres.id', '=', 'games.genre_id')->orderBy('genres.name', $filter['order']),
            'creator' => $entries->join('users', 'users.id', '=', 'games.user_id')->orderBy('users.name', $filter['order']),
        };

        return Inertia::render('admin/Game', [
            'games' => AdminGameListResource::collection($entries->paginate(50))
        ]);
    }
}
