<?php

namespace App\Services;

use App\Enum\GameCollectionType;
use App\Models\Game;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class UserGameListsServices
{
	public static function checkIfGameIsInAnyUserGamesList(Game $game): ?array
	{
		/** @var User */
		$user = Auth::user();

		if (!$user) {
			return null;
		}

		$listToUse = self::getList($game);

		$games = $user->games()->where('slug', $game->slug)->withPivot(['list_type'])->wherePivotIn('list_type', $listToUse)->get();

		$lists = [];

		foreach ($listToUse as $key) {
			$lists[$key] = $games->contains(function ($userGame) use ($key) {
				return $userGame->pivot->list_type === $key;
			});
		}

		return $lists;
	}

	public static function getList(Game $game): array
	{
		if (!$game->isGameReleased()) {
			return [GameCollectionType::WISHLIST->value];
		}

		return array_column(GameCollectionType::cases(), 'value');
	}
}
