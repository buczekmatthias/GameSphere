<?php

namespace App\Http\Controllers;

use App\Enum\HomepageSortingType;
use App\Http\Resources\Games\GamesListResource;
use App\Models\Game;
use Inertia\Inertia;
use Inertia\Response;

class HomepageController extends Controller
{
	public function __invoke(string $type = 'newest'): Response
	{
		return Inertia::render('app/Homepage', [
			'games' => Inertia::defer(
				function () use ($type) {
					$games = $type === 'upcoming' ? Game::query() : Game::gamesWithScore();

					$games = match ($type) {
						'newest' => $games->orderBy('released_at', 'DESC')->where('released_at', '<=', now()->endOfDay()),
						'most_popular' => $games->whereHas('favoriteUsers')
							->withCount('favoriteUsers')
							->orderBy('favorite_users_count', 'DESC'),
						'upcoming' => $games->orderBy('released_at', 'DESC')->where('released_at', '>=', now()->startOfDay()->addDay()),
					};

					return GamesListResource::collection($games->limit(40)->get());
				}
			),
			'activeType' => $type,
			'types' => array_map(
				fn ($t) => ['name' => $t, 'route' => route('home', ['type' => $t])],
				array_column(HomepageSortingType::cases(), 'value')
			)
		]);
	}
}
