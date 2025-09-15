<?php

namespace App\Services;

use App\Enum\GameCollectionType;
use App\Models\Game;
use App\Models\User;
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

		$lists = [];

		foreach (array_column(GameCollectionType::cases(), 'value') as $key) {
			$lists[$key] = $games->contains(function ($userGame) use ($game, $key) {
				return $userGame->slug === $game->slug && $userGame->pivot->list_type === $key;
			});
		}

		return $lists;
	}
}
