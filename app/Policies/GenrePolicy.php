<?php

namespace App\Policies;

use App\Models\Genre;
use App\Models\User;

class GenrePolicy
{
	/**
	 * Determine whether the user can create models.
	 */
	public function create(User $user): bool
	{
		return !is_null($user) && $user->isStaff();
	}

	/**
	 * Determine whether the user can update the model.
	 */
	public function update(User $user, Genre $genre): bool
	{
		return !is_null($user) && $user->isStaff();
	}

	/**
	 * Determine whether the user can delete the model.
	 */
	public function delete(User $user, Genre $genre): bool
	{
		return !is_null($user) && $user->isStaff();
	}
}
