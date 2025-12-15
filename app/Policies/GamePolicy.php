<?php

namespace App\Policies;

use App\Models\Game;
use App\Models\User;

class GamePolicy
{
	/**
	 * Determine whether the user can create models.
	 */
	public function create(User $user): bool
	{
		return !is_null($user) && $user->canAddGame();
	}

	/**
	 * Determine whether the user can update the model.
	 */
	public function update(User $user, Game $game): bool
	{
		if (is_null($user)) {
			return false;
		}

		if ($user->isStaff()) {
			return true;
		}

		if (is_null($game->user_id)) {
			return false;
		}

		return $game->user_id === $user->id;
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(User $user, Game $game): bool
	{
		if (is_null($user)) {
			return false;
		}

		if ($user->isStaff()) {
			return true;
		}

		if (is_null($game->user_id)) {
			return false;
		}

		return $game->user_id === $user->id;
	}
}
