<?php

namespace App\Services;

use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

final class UserGameListsServices
{
	public static function checkIfGameIsInAnyUserGamesList(Game $game)
	{
		/** @var User */
		$user = Auth::user();

		if (!$user) {
			return null;
		}

		$games = $user->games()->withPivot(['list_type'])->get();

		return [
			'wishlist' => self::checkIfGameIsInUserGameList($games, $game, 'wishlist'),
			'owned' => self::checkIfGameIsInUserGameList($games, $game, 'owned'),
			'playing' => self::checkIfGameIsInUserGameList($games, $game, 'playing'),
			'completed' => self::checkIfGameIsInUserGameList($games, $game, 'completed'),
			'favorite' => self::checkIfGameIsInUserGameList($games, $game, 'favorite'),
			'upcoming_releases' => self::checkIfGameIsInUserGameList($games, $game, 'upcoming_releases')
		];
	}

	public static function checkIfGameIsInUserGameList(Collection $games, Game $game, string $list)
	{
		return $games->contains(function ($userGame) use ($game, $list) {
			return $userGame->slug === $game->slug && $userGame->pivot->list_type === $list;
		});
	}
}
