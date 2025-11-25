<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Game\GameListResource;
use App\Models\Game;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GameController extends Controller
{
	private const SORT_COLUMNS = ['title', 'media', 'genre', 'creator', 'released_at', 'created_at'];

	/**
	 * Handle the incoming request.
	 */
	public function __invoke(Request $request): Response
	{
		$entries = Game::with(['creator', 'genre']);
		$column = strtolower($request->get('column', 'title'));
		$order = strtolower($request->get('order', 'asc'));

		if (!in_array(strtolower($order), self::ORDER)) {
			$order = 'asc';
		}
		if (!in_array($column, self::SORT_COLUMNS)) {
			$column = 'title';
		}

		match ($column) {
			'title', 'released_at', 'created_at' => $entries->orderBy($column, $order),
			'media' => $entries->orderByRaw("json_array_length(media) {$order}"),
			'genre' => $entries->join('genres', 'genres.id', '=', 'games.genre_id')->orderBy('genres.name', $order),
			'creator' => $entries->join('users', 'users.id', '=', 'games.user_id')->orderBy('users.name', $order),
		};

		return Inertia::render('admin/Game', [
			'games' => GameListResource::collection($entries->paginate(50))
		]);
	}
}
